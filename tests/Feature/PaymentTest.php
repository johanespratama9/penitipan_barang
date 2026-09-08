<?php

namespace Tests\Feature;

use App\Models\Consignor;
use App\Models\ConsignorPayment;
use App\Models\User;
use App\Services\ConsignorBalanceService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $penitip;
    protected Consignor $consignor;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_p@test.com'],
            ['name' => 'Admin Payment', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->penitip = User::firstOrCreate(
            ['email' => 'penitip_p@test.com'],
            ['name' => 'Penitip Payment', 'password' => bcrypt('password')]
        );
        $this->penitip->syncRoles(['penitip']);

        $this->consignor = Consignor::create([
            'user_id' => $this->penitip->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        // Beri saldo awal Rp 1.000.000 ke penitip
        app(ConsignorBalanceService::class)->recordSale($this->consignor, 1200000, 200000, 1000000);
    }

    public function test_payment_deducts_consignor_balance(): void
    {
        $paymentService = app(PaymentService::class);
        $payment = $paymentService->createPayment(
            $this->consignor,
            400000,
            'transfer',
            'TRX-123456',
            'Pencairan dana tahap 1',
            $this->admin->id
        );

        $this->assertNotEmpty($payment->payment_number);
        $this->assertStringStartsWith('PAY-', $payment->payment_number);
        $this->assertEquals(400000, (float) $payment->amount);

        // Saldo tersisa harus Rp 600.000
        $balance = $this->consignor->fresh()->balance;
        $this->assertEquals(600000, (float) $balance->balance);
        $this->assertEquals(400000, (float) $balance->total_paid);
    }

    public function test_cannot_pay_more_than_available_balance(): void
    {
        $this->expectException(\Exception::class);

        $paymentService = app(PaymentService::class);
        // Coba bayar Rp 1.500.000 (melebihi saldo Rp 1.000.000)
        $paymentService->createPayment($this->consignor, 1500000, 'transfer');
    }

    public function test_penitip_can_only_view_own_payment(): void
    {
        $payment = ConsignorPayment::create([
            'consignor_id' => $this->consignor->id,
            'amount' => 200000,
            'payment_method' => 'transfer',
            'paid_by' => $this->admin->id,
        ]);

        $this->assertTrue($this->penitip->can('view', $payment));

        // Penitip lain
        $otherUser = User::create(['name' => 'Other', 'email' => 'other@test.com', 'password' => bcrypt('p')]);
        $otherUser->syncRoles(['penitip']);
        $this->assertFalse($otherUser->can('view', $payment));
    }
}

