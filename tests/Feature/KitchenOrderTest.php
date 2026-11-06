<?php

namespace Tests\Feature;

use App\Filament\Pages\KitchenPage;
use App\Livewire\KitchenDisplay;
use App\Livewire\PosTerminal;
use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KitchenOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir_dapur@test.com'],
            ['name' => 'Kasir Dapur', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_dapur@test.com'],
            ['name' => 'Admin Dapur', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $category = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $consignor = Consignor::create([
            'name'   => 'Dapur Utama',
            'phone'  => '081234567890',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'category_id'      => $category->id,
            'consignor_id'     => $consignor->id,
            'code'             => 'FD-01',
            'barcode'          => '888123456789',
            'name'             => 'Nasi Goreng Spesial',
            'selling_price'    => 25000,
            'commission_type'  => 'percentage',
            'commission_value' => 15,
            'stock'            => 50,
            'status'           => 'available',
        ]);
    }

    /**
     * 1. Orderan masuk dari kasir (POS) -> table_number & order_notes tersimpan
     *    dan kitchen_status otomatis 'pending'.
     */
    public function test_order_from_pos_enters_kitchen_as_pending(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->set('table_number', 'Meja 07')
            ->set('order_notes', 'Pedas sedang, ekstra kerupuk')
            ->set('customer_name', 'Budi Santoso')
            ->set('payment_method', 'cash')
            ->set('paid_amount', 30000)
            ->call('checkout')
            ->assertDispatched('play-success-sound');

        $sale = Sale::latest()->first();

        $this->assertNotNull($sale);
        $this->assertEquals('Meja 07', $sale->table_number);
        $this->assertEquals('Pedas sedang, ekstra kerupuk', $sale->order_notes);
        $this->assertEquals('pending', $sale->kitchen_status);
        $this->assertEquals('Budi Santoso', $sale->customer_name);
    }

    /**
     * 2. Akses Layar Dapur: Kasir dan Admin bisa akses /dapur dan /dasbor/dapur
     */
    public function test_kitchen_display_access(): void
    {
        $this->actingAs($this->kasir);
        $response = $this->get('/dapur');
        $response->assertStatus(200);
        $response->assertSee('SISTEM ORDER DAPUR');

        $this->actingAs($this->admin);
        $responseAdmin = $this->get('/dasbor/dapur');
        $responseAdmin->assertStatus(200);

        // Guest diarahkan ke login
        auth()->logout();
        $responseGuest = $this->get('/dapur');
        $responseGuest->assertRedirect('/login');
    }

    /**
     * 3. Siklus Order Dapur Lengkap:
     *    Order masuk (pending) -> Dimasak (cooking) -> Siap Saji (ready) -> ALL DONE (completed)
     */
    public function test_full_kitchen_order_lifecycle(): void
    {
        $this->actingAs($this->kasir);

        // Buat sale pending
        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-001',
            'cashier_id'     => $this->kasir->id,
            'customer_name'  => 'Andi',
            'table_number'   => 'Meja 03',
            'order_notes'    => 'Jangan pakai seledri',
            'kitchen_status' => 'pending',
            'total'          => 25000,
            'subtotal'       => 25000,
            'discount'       => 0,
            'paid_amount'    => 25000,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'sold_at'        => now(),
        ]);

        $sale->items()->create([
            'product_id'       => $this->product->id,
            'product_name'     => $this->product->name,
            'consignor_id'     => $this->product->consignor_id,
            'quantity'         => 1,
            'unit_cost'        => 20000,
            'selling_price'    => 25000,
            'subtotal'         => 25000,
            'consignor_amount' => 21250,
            'commission_amount'=> 3750,
        ]);

        // Uji di Layar Dapur Livewire
        $testKds = Livewire::test(KitchenDisplay::class);

        // Tahap 1: Mulai Dimasak (pending -> cooking)
        $testKds->call('startCooking', $sale->id);
        $sale->refresh();
        $this->assertEquals('cooking', $sale->kitchen_status);
        $this->assertNotNull($sale->cooking_started_at);

        // Tahap 2: Tandai Siap Saji (cooking -> ready)
        $testKds->call('markReady', $sale->id);
        $sale->refresh();
        $this->assertEquals('ready', $sale->kitchen_status);
        $this->assertNotNull($sale->cooking_finished_at);

        // Tahap 3: Tandai Selesai Semua / ALL DONE (ready -> completed)
        $testKds->call('markCompleted', $sale->id);
        $sale->refresh();
        $this->assertEquals('completed', $sale->kitchen_status);
        $this->assertNotNull($sale->kitchen_completed_at);
    }

    /**
     * 4. Cetak Tiket Bon Dapur mencatat kitchen_printed_at
     */
    public function test_print_kitchen_ticket_records_timestamp(): void
    {
        $this->actingAs($this->kasir);

        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-002',
            'cashier_id'     => $this->kasir->id,
            'customer_name'  => 'Citra',
            'table_number'   => 'Meja 05',
            'kitchen_status' => 'pending',
            'total'          => 25000,
            'subtotal'       => 25000,
            'discount'       => 0,
            'paid_amount'    => 25000,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'sold_at'        => now(),
        ]);

        $sale->items()->create([
            'product_id'       => $this->product->id,
            'product_name'     => $this->product->name,
            'consignor_id'     => $this->product->consignor_id,
            'quantity'         => 1,
            'unit_cost'        => 20000,
            'selling_price'    => 25000,
            'subtotal'         => 25000,
            'consignor_amount' => 21250,
            'commission_amount'=> 3750,
        ]);

        Livewire::test(KitchenDisplay::class)
            ->call('printKitchenTicket', $sale->id)
            ->assertSet('selectedTicketForPrint.invoice_number', 'INV-TEST-002')
            ->assertSet('selectedTicketForPrint.table_number', 'Meja 05');

        $sale->refresh();
        $this->assertNotNull($sale->kitchen_printed_at);
    }

    /**
     * 5. Filament KitchenPage juga memiliki fungsionalitas yang sama
     */
    public function test_filament_kitchen_page_actions(): void
    {
        $this->actingAs($this->admin);

        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-003',
            'cashier_id'     => $this->admin->id,
            'table_number'   => 'Meja 01',
            'kitchen_status' => 'pending',
            'total'          => 25000,
            'subtotal'       => 25000,
            'discount'       => 0,
            'paid_amount'    => 25000,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'sold_at'        => now(),
        ]);

        Livewire::test(KitchenPage::class)
            ->call('startCooking', $sale->id)
            ->call('markReady', $sale->id)
            ->call('markCompleted', $sale->id);

        $sale->refresh();
        $this->assertEquals('completed', $sale->kitchen_status);
        $this->assertNotNull($sale->kitchen_completed_at);
    }

    /**
     * 6. User dengan role dapur dapat mengakses Layar Dapur dan mengoperasikannya
     */
    public function test_dapur_role_user_can_access_kitchen_display(): void
    {
        Role::firstOrCreate(['name' => 'dapur', 'guard_name' => 'web']);

        $dapur = User::firstOrCreate(
            ['email' => 'koki@test.com'],
            ['name' => 'Koki Dapur', 'password' => bcrypt('password')]
        );
        $dapur->syncRoles(['dapur']);

        $this->actingAs($dapur);

        $response = $this->get('/dapur');
        $response->assertStatus(200);
        $response->assertSee('SISTEM ORDER DAPUR');
        $response->assertSee('Koki Dapur');

        // Buka via Filament panel /dasbor/dapur juga bisa
        $responseFilament = $this->get('/dasbor/dapur');
        $responseFilament->assertStatus(200);
    }

    /**
     * 7. Keamanan Role Dapur: Terisolasi (sandboxed) dan tidak bisa akses POS maupun Admin
     */
    public function test_dapur_role_user_is_sandboxed_from_pos_and_admin_panels(): void
    {
        Role::firstOrCreate(['name' => 'dapur', 'guard_name' => 'web']);

        $dapur = User::firstOrCreate(
            ['email' => 'koki_secure@test.com'],
            ['name' => 'Koki Secure', 'password' => bcrypt('password')]
        );
        $dapur->syncRoles(['dapur']);

        $this->actingAs($dapur);

        // Akses /pos diarahkan langsung ke /dapur
        $responsePos = $this->get('/pos');
        $responsePos->assertRedirect('/dapur');

        // Akses dashboard admin /dasbor diarahkan ke /dapur
        $responseDashboard = $this->get('/dasbor');
        $responseDashboard->assertRedirect('/dapur');

        // Akses kelola pengguna /dasbor/users diarahkan ke /dapur
        $responseUsers = $this->get('/dasbor/users');
        $responseUsers->assertRedirect('/dapur');

        // Akses laporan /dasbor/reports-page diarahkan ke /dapur
        $responseReports = $this->get('/dasbor/reports-page');
        $responseReports->assertRedirect('/dapur');
    }

    /**
     * 8. Role yang tidak berwenang (misal penitip) dilarang mengakses Layar Dapur
     */
    public function test_unauthorized_role_cannot_access_kitchen_display(): void
    {
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $penitip = User::firstOrCreate(
            ['email' => 'penitip_coba@test.com'],
            ['name' => 'Penitip Coba', 'password' => bcrypt('password')]
        );
        $penitip->syncRoles(['penitip']);

        $this->actingAs($penitip);

        $response = $this->get('/dapur');
        $response->assertStatus(403);
    }
}
