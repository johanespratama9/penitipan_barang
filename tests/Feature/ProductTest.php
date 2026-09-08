<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;
    protected User $penitip1;
    protected User $penitip2;
    protected Category $category;
    protected Consignor $consignor1;
    protected Consignor $consignor2;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_prod@test.com'],
            ['name' => 'Admin Product', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir_prod@test.com'],
            ['name' => 'Kasir Product', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $this->penitip1 = User::firstOrCreate(
            ['email' => 'penitip1_prod@test.com'],
            ['name' => 'Penitip Satu', 'password' => bcrypt('password')]
        );
        $this->penitip1->syncRoles(['penitip']);

        $this->penitip2 = User::firstOrCreate(
            ['email' => 'penitip2_prod@test.com'],
            ['name' => 'Penitip Dua', 'password' => bcrypt('password')]
        );
        $this->penitip2->syncRoles(['penitip']);

        $this->category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $this->consignor1 = Consignor::create([
            'user_id' => $this->penitip1->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $this->consignor2 = Consignor::create([
            'user_id' => $this->penitip2->id,
            'name' => 'Siti Rahmawati',
            'phone' => '085678901234',
            'status' => 'active',
        ]);

        $this->product1 = Product::create([
            'category_id' => $this->category->id,
            'consignor_id' => $this->consignor1->id,
            'name' => 'Mouse Wireless Logitech',
            'selling_price' => 200000,
            'commission_type' => 'percentage',
            'commission_value' => 20,
            'stock' => 5,
            'status' => 'available',
        ]);

        $this->product2 = Product::create([
            'category_id' => $this->category->id,
            'consignor_id' => $this->consignor2->id,
            'name' => 'Keyboard Mechanical',
            'selling_price' => 500000,
            'commission_type' => 'fixed',
            'commission_value' => 50000,
            'stock' => 2,
            'status' => 'available',
        ]);
    }

    public function test_product_creation_with_auto_generated_code(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'consignor_id' => $this->consignor1->id,
            'name' => 'Smartwatch Xiaomi',
            'selling_price' => 300000,
            'commission_type' => 'percentage',
            'commission_value' => 15,
            'stock' => 3,
            'status' => 'available',
        ]);

        $this->assertNotEmpty($product->code);
        $this->assertStringStartsWith('PRD-', $product->code);
        $this->assertNotNull($product->received_at);
        $this->assertEquals(255000, (float) $product->consignor_price); // 300.000 - 15% (45.000) = 255.000
    }

    public function test_commission_service_percentage_and_fixed_calculation(): void
    {
        $service = new CommissionService();

        // 20% dari Rp 100.000
        $resultPercentage = $service->calculate(100000, 'percentage', 20);
        $this->assertEquals(20000, $resultPercentage['store_commission']);
        $this->assertEquals(80000, $resultPercentage['consignor_amount']);

        // Fixed Rp 10.000 dari Rp 100.000
        $resultFixed = $service->calculate(100000, 'fixed', 10000);
        $this->assertEquals(10000, $resultFixed['store_commission']);
        $this->assertEquals(90000, $resultFixed['consignor_amount']);
    }

    public function test_product_code_must_be_unique(): void
    {
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Product::create([
            'category_id' => $this->category->id,
            'consignor_id' => $this->consignor1->id,
            'code' => $this->product1->code,
            'name' => 'Duplikat Kode Produk',
            'selling_price' => 100000,
        ]);
    }

    public function test_product_barcode_must_be_unique(): void
    {
        $this->product1->update(['barcode' => 'BARCODE12345']);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Product::create([
            'category_id' => $this->category->id,
            'consignor_id' => $this->consignor2->id,
            'name' => 'Duplikat Barcode Produk',
            'barcode' => 'BARCODE12345',
            'selling_price' => 100000,
        ]);
    }

    public function test_admin_has_full_product_permissions(): void
    {
        $this->assertTrue($this->admin->can('viewAny', Product::class));
        $this->assertTrue($this->admin->can('view', $this->product1));
        $this->assertTrue($this->admin->can('create', Product::class));
        $this->assertTrue($this->admin->can('update', $this->product1));
        $this->assertTrue($this->admin->can('delete', $this->product1));
    }

    public function test_kasir_can_only_view_products(): void
    {
        $this->assertTrue($this->kasir->can('viewAny', Product::class));
        $this->assertTrue($this->kasir->can('view', $this->product1));
        $this->assertFalse($this->kasir->can('create', Product::class));
        $this->assertFalse($this->kasir->can('update', $this->product1));
        $this->assertFalse($this->kasir->can('delete', $this->product1));
    }

    public function test_penitip_can_only_view_own_products(): void
    {
        // Penitip 1 can view own product
        $this->assertTrue($this->penitip1->can('view', $this->product1));

        // Penitip 1 CANNOT view penitip 2's product
        $this->assertFalse($this->penitip1->can('view', $this->product2));

        // Penitip cannot create, update, or delete products
        $this->assertFalse($this->penitip1->can('create', Product::class));
        $this->assertFalse($this->penitip1->can('update', $this->product1));
        $this->assertFalse($this->penitip1->can('delete', $this->product1));
    }
}

