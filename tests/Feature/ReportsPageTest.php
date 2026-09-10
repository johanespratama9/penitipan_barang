<?php

namespace Tests\Feature;

use App\Filament\Pages\ReportsPage;
use App\Models\Category;
use App\Models\Consignor;
use App\Models\ConsignorPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportsPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;
    protected Consignor $consignor;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_report@test.com'],
            ['name' => 'Admin Report', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir_report@test.com'],
            ['name' => 'Kasir Report', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $category = Category::create(['name' => 'Aksesoris', 'slug' => 'aksesoris']);
        $this->consignor = Consignor::create([
            'name' => 'Penitip Sukses',
            'phone' => '081299990001',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'consignor_id' => $this->consignor->id,
            'code' => 'PRD-RPT-01',
            'barcode' => '777000000001',
            'name' => 'Tumbler Sarinah',
            'selling_price' => 50000,
            'commission_type' => 'percentage',
            'commission_value' => 20,
            'stock' => 5,
            'status' => 'available',
        ]);
    }

    public function test_admin_can_access_reports_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports-page');

        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Laporan Penitip &amp; Saldo', false);
        $response->assertSee('Laporan Produk &amp; Stok', false);
        $response->assertSee('Laporan Pencairan Dana');
        $response->assertSee('Export CSV');
    }

    public function test_reports_page_tabs_and_search_filter(): void
    {
        // Buat data sale
        $sale = Sale::create([
            'invoice_number' => 'INV-2026-TEST01',
            'cashier_id' => $this->admin->id,
            'customer_name' => 'Pelanggan Setia',
            'subtotal' => 50000,
            'discount' => 0,
            'total' => 50000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'payment_method' => 'qris',
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'consignor_id' => $this->consignor->id,
            'product_name' => $this->product->name,
            'product_code' => $this->product->code,
            'quantity' => 1,
            'selling_price' => 50000,
            'subtotal' => 50000,
            'consignor_amount' => 40000,
            'commission_amount' => 10000,
        ]);

        $this->actingAs($this->admin);

        // Test Livewire tabs dan search
        Livewire::test(ReportsPage::class)
            ->assertSet('activeTab', 'sales')
            ->assertSee('INV-2026-TEST01')
            ->assertSee('Pelanggan Setia')
            ->set('search', 'TEST01')
            ->assertSee('INV-2026-TEST01')
            ->set('search', 'NOMATCHXYZ')
            ->assertDontSee('INV-2026-TEST01')
            // Ganti tab ke consignor
            ->set('activeTab', 'consignor')
            ->assertSee('Penitip Sukses')
            // Ganti tab ke products
            ->set('activeTab', 'products')
            ->assertSee('Tumbler Sarinah')
            // Ganti tab ke payments
            ->set('activeTab', 'payments');
    }

    public function test_reports_page_export_csv_download(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(ReportsPage::class);
        $response = $component->instance()->exportCsv('sales');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}

