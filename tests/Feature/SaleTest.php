<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected User $admin;
    protected User $penitip;
    protected Consignor $consignor;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_s@test.com'],
            ['name' => 'Admin S', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->cashier = User::firstOrCreate(
            ['email' => 'cashier_s@test.com'],
            ['name' => 'Kasir S', 'password' => bcrypt('password')]
        );
        $this->cashier->syncRoles(['kasir']);

        $this->penitip = User::firstOrCreate(
            ['email' => 'penitip_s@test.com'],
            ['name' => 'Penitip S', 'password' => bcrypt('password')]
        );
        $this->penitip->syncRoles(['penitip']);

        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $this->consignor = Consignor::create([
            'user_id' => $this->penitip->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'consignor_id' => $this->consignor->id,
            'name' => 'Kamera Mirrorless',
            'selling_price' => 5000000,
            'commission_type' => 'percentage',
            'commission_value' => 10, // 10% toko (500rb), 90% penitip (4.5jt)
            'stock' => 2,
            'status' => 'available',
        ]);
    }

    public function test_sale_creation_reduces_stock_and_updates_consignor_balance(): void
    {
        $saleService = app(SaleService::class);

        $sale = $saleService->createSale(
            [
                'customer_name' => 'Andi Wijaya',
                'discount' => 0,
                'paid_amount' => 5000000,
                'payment_method' => 'cash',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
            $this->cashier->id
        );

        $this->assertNotEmpty($sale->invoice_number);
        $this->assertStringStartsWith('INV-', $sale->invoice_number);
        $this->assertEquals(5000000, (float) $sale->total);

        // Stok berkurang
        $this->product->refresh();
        $this->assertEquals(1, $this->product->stock);

        // Mutasi stok tercatat
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'reference_type' => 'sale',
            'reference_id' => $sale->id,
            'quantity' => 1,
        ]);

        // Saldo penitip terupdate
        $balance = $this->consignor->fresh()->balance;
        $this->assertNotNull($balance);
        $this->assertEquals(5000000, (float) $balance->total_sales);
        $this->assertEquals(500000, (float) $balance->total_commission);
        $this->assertEquals(4500000, (float) $balance->total_earned);
        $this->assertEquals(4500000, (float) $balance->balance);
    }

    public function test_sale_fails_if_stock_insufficient(): void
    {
        $this->expectException(\Exception::class);

        $saleService = app(SaleService::class);
        $saleService->createSale(
            ['customer_name' => 'Andi'],
            [['product_id' => $this->product->id, 'quantity' => 10]],
            $this->cashier->id
        );
    }

    public function test_kasir_and_admin_can_access_sales(): void
    {
        $sale = Sale::create([
            'cashier_id' => $this->cashier->id,
            'total' => 100000,
            'paid_amount' => 100000,
        ]);

        $this->assertTrue($this->admin->can('viewAny', Sale::class));
        $this->assertTrue($this->admin->can('view', $sale));
        $this->assertTrue($this->cashier->can('viewAny', Sale::class));
        $this->assertTrue($this->cashier->can('view', $sale));

        // Penitip tidak dapat mengakses menu penjualan umum
        $this->assertFalse($this->penitip->can('viewAny', Sale::class));
    }
}

