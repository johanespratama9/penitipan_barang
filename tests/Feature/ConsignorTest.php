<?php

namespace Tests\Feature;

use App\Models\Consignor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConsignorTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;
    protected User $penitip1;
    protected User $penitip2;
    protected Consignor $consignor1;
    protected Consignor $consignor2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@test.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir_test@test.com'],
            ['name' => 'Kasir Test', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $this->penitip1 = User::firstOrCreate(
            ['email' => 'penitip1@test.com'],
            ['name' => 'Penitip Satu', 'password' => bcrypt('password')]
        );
        $this->penitip1->syncRoles(['penitip']);

        $this->penitip2 = User::firstOrCreate(
            ['email' => 'penitip2@test.com'],
            ['name' => 'Penitip Dua', 'password' => bcrypt('password')]
        );
        $this->penitip2->syncRoles(['penitip']);

        $this->consignor1 = Consignor::create([
            'user_id' => $this->penitip1->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'penitip1@test.com',
            'status' => 'active',
        ]);

        $this->consignor2 = Consignor::create([
            'user_id' => $this->penitip2->id,
            'name' => 'Siti Rahmawati',
            'phone' => '085678901234',
            'email' => 'penitip2@test.com',
            'status' => 'active',
        ]);
    }

    public function test_consignor_can_be_created_with_auto_generated_code(): void
    {
        $consignor = Consignor::create([
            'name' => 'Ahmad Dahlan',
            'phone' => '089876543210',
            'status' => 'active',
        ]);

        $this->assertNotEmpty($consignor->code);
        $this->assertStringStartsWith('PEN-', $consignor->code);
        $this->assertDatabaseHas('consignors', [
            'id' => $consignor->id,
            'name' => 'Ahmad Dahlan',
            'status' => 'active',
        ]);
    }

    public function test_consignor_code_must_be_unique(): void
    {
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Consignor::create([
            'code' => $this->consignor1->code,
            'name' => 'Duplikat Code',
            'phone' => '081111111111',
        ]);
    }

    public function test_consignor_user_relationship(): void
    {
        $this->assertEquals($this->penitip1->id, $this->consignor1->user->id);
        $this->assertEquals($this->consignor1->id, $this->penitip1->consignor->id);
    }

    public function test_admin_has_full_consignor_permissions(): void
    {
        $this->assertTrue($this->admin->can('viewAny', Consignor::class));
        $this->assertTrue($this->admin->can('view', $this->consignor1));
        $this->assertTrue($this->admin->can('create', Consignor::class));
        $this->assertTrue($this->admin->can('update', $this->consignor1));
        $this->assertTrue($this->admin->can('delete', $this->consignor1));
    }

    public function test_kasir_can_only_view_consignors(): void
    {
        $this->assertTrue($this->kasir->can('viewAny', Consignor::class));
        $this->assertTrue($this->kasir->can('view', $this->consignor1));
        $this->assertFalse($this->kasir->can('create', Consignor::class));
        $this->assertFalse($this->kasir->can('update', $this->consignor1));
        $this->assertFalse($this->kasir->can('delete', $this->consignor1));
    }

    public function test_penitip_can_only_view_own_profile(): void
    {
        // Penitip 1 can view own profile
        $this->assertTrue($this->penitip1->can('view', $this->consignor1));

        // Penitip 1 CANNOT view penitip 2's profile
        $this->assertFalse($this->penitip1->can('view', $this->consignor2));

        // Penitip cannot create, update, or delete
        $this->assertFalse($this->penitip1->can('create', Consignor::class));
        $this->assertFalse($this->penitip1->can('update', $this->consignor1));
        $this->assertFalse($this->penitip1->can('delete', $this->consignor1));
    }
}

