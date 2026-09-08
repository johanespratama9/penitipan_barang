<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\Consignor;
use App\Models\Product;
use App\Models\User;
use App\Services\ConsignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConsignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;
    protected User $penitip1;
    protected User $penitip2;
    protected Category $category;
    protected Consignor $consignor1;
    protected Consignor $consignor2;
    protected Consignment $consignment1;
    protected Consignment $consignment2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_csg@test.com'],
            ['name' => 'Admin Consignment', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir_csg@test.com'],
            ['name' => 'Kasir Consignment', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $this->penitip1 = User::firstOrCreate(
            ['email' => 'penitip1_csg@test.com'],
            ['name' => 'Penitip Satu', 'password' => bcrypt('password')]
        );
        $this->penitip1->syncRoles(['penitip']);

        $this->penitip2 = User::firstOrCreate(
            ['email' => 'penitip2_csg@test.com'],
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

        $service = app(ConsignmentService::class);

        $this->consignment1 = $service->createConsignment(
            $this->consignor1,
            [
                'status' => 'received',
                'notes' => 'Barang titipan Budi',
            ],
            [
                [
                    'product_name' => 'Mouse Gaming RGB',
                    'category_id' => $this->category->id,
                    'quantity' => 2,
                    'purchase_price' => 150000,
                    'selling_price' => 200000,
                    'commission_type' => 'percentage',
                    'commission_value' => 20,
                    'consignor_amount' => 160000,
                ],
            ]
        );

        $this->consignment2 = $service->createConsignment(
            $this->consignor2,
            [
                'status' => 'draft',
                'notes' => 'Barang titipan Siti',
            ],
            [
                [
                    'product_name' => 'Tas Kulit Wanita',
                    'category_id' => $this->category->id,
                    'quantity' => 1,
                    'purchase_price' => 300000,
                    'selling_price' => 400000,
                    'commission_type' => 'fixed',
                    'commission_value' => 50000,
                    'consignor_amount' => 350000,
                ],
            ]
        );
    }

    public function test_consignment_creation_and_auto_code(): void
    {
        $this->assertNotEmpty($this->consignment1->code);
        $this->assertStringStartsWith('CSG-', $this->consignment1->code);
        $this->assertEquals(2, $this->consignment1->total_items);
        $this->assertCount(1, $this->consignment1->items);
    }

    public function test_consignment_approval_creates_available_products(): void
    {
        $service = app(ConsignmentService::class);
        $service->approveConsignment($this->consignment1);

        $this->consignment1->refresh();
        $this->assertEquals('approved', $this->consignment1->status);

        $item = $this->consignment1->items->first();
        $this->assertEquals('approved', $item->status);
        $this->assertNotNull($item->product_id);

        // Pastikan produk otomatis terbuat dan berstatus available
        $product = Product::find($item->product_id);
        $this->assertNotNull($product);
        $this->assertEquals('Mouse Gaming RGB', $product->name);
        $this->assertEquals('available', $product->status);
        $this->assertEquals(2, $product->stock);
        $this->assertEquals(200000, (float) $product->selling_price);
        $this->assertEquals(160000, (float) $product->consignor_price);
    }

    public function test_consignment_rejection(): void
    {
        $service = app(ConsignmentService::class);
        $service->rejectConsignment($this->consignment2, 'Barang tidak sesuai kriteria toko.');

        $this->consignment2->refresh();
        $this->assertEquals('rejected', $this->consignment2->status);
        $this->assertStringContainsString('Alasan Ditolak', $this->consignment2->notes);
        $this->assertEquals('rejected', $this->consignment2->items->first()->status);
    }

    public function test_admin_has_full_consignment_permissions(): void
    {
        $this->assertTrue($this->admin->can('viewAny', Consignment::class));
        $this->assertTrue($this->admin->can('view', $this->consignment1));
        $this->assertTrue($this->admin->can('create', Consignment::class));
        $this->assertTrue($this->admin->can('update', $this->consignment1));
        $this->assertTrue($this->admin->can('delete', $this->consignment1));
    }

    public function test_kasir_can_only_view_consignments(): void
    {
        $this->assertTrue($this->kasir->can('viewAny', Consignment::class));
        $this->assertTrue($this->kasir->can('view', $this->consignment1));
        $this->assertFalse($this->kasir->can('create', Consignment::class));
        $this->assertFalse($this->kasir->can('update', $this->consignment1));
        $this->assertFalse($this->kasir->can('delete', $this->consignment1));
    }

    public function test_penitip_can_only_view_own_consignments(): void
    {
        // Penitip 1 can view own consignment
        $this->assertTrue($this->penitip1->can('view', $this->consignment1));

        // Penitip 1 CANNOT view penitip 2's consignment
        $this->assertFalse($this->penitip1->can('view', $this->consignment2));

        // Penitip cannot create, update, or delete directly
        $this->assertFalse($this->penitip1->can('create', Consignment::class));
        $this->assertFalse($this->penitip1->can('update', $this->consignment1));
        $this->assertFalse($this->penitip1->can('delete', $this->consignment1));
    }
}
