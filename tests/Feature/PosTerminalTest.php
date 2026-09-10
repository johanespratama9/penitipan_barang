<?php

namespace Tests\Feature;

use App\Livewire\PosTerminal;
use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosTerminalTest extends TestCase
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
            ['email' => 'kasir_terminal@test.com'],
            ['name' => 'Kasir Terminal', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_terminal@test.com'],
            ['name' => 'Admin Terminal', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $category  = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);
        $consignor = Consignor::create([
            'name'   => 'Budi Supplier',
            'phone'  => '081200000001',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'category_id'      => $category->id,
            'consignor_id'     => $consignor->id,
            'code'             => 'PRD-TRM-01',
            'barcode'          => '888000000001',
            'name'             => 'Americano',
            'selling_price'    => 22000,
            'commission_type'  => 'percentage',
            'commission_value' => 20,
            'stock'            => 10,
            'status'           => 'available',
        ]);
    }

    /** Kasir bisa mengakses /pos dan melihat POS terminal */
    public function test_kasir_can_access_pos_terminal(): void
    {
        $this->actingAs($this->kasir);

        $response = $this->get('/pos');

        $response->assertStatus(200);
        $response->assertSee('SARINAH STREET');
        $response->assertSee('ORDER #');
        $response->assertSee('Scan Kamera HP');
    }

    /** Admin juga bisa mengakses /pos */
    public function test_admin_can_access_pos_terminal(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/pos');

        $response->assertStatus(200);
        $response->assertSee('SARINAH STREET');
    }

    /** Tamu yang belum login tidak bisa akses /pos */
    public function test_guest_cannot_access_pos_terminal(): void
    {
        $response = $this->get('/pos');

        // Redirect ke /login (yang kemudian ke /admin/login)
        $response->assertRedirect('/login');
    }

    /** Livewire component PosTerminal bisa render dan add to cart */
    public function test_pos_terminal_livewire_add_to_cart(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->assertCount('cart', 1)
            ->assertSet('cart.' . $this->product->id . '.quantity', 1)
            ->assertSet('cart.' . $this->product->id . '.price', 22000.0);
    }

    /** Barcode scan langsung menambahkan produk ke cart */
    public function test_pos_terminal_scan_barcode_direct(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(PosTerminal::class)
            ->call('scanBarcodeDirect', '888000000001')
            ->assertCount('cart', 1)
            ->assertSet('cart.' . $this->product->id . '.name', 'Americano');
    }

    /** Checkout modal bisa dibuka dan ditutup */
    public function test_pos_terminal_checkout_modal(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(PosTerminal::class)
            ->assertSet('isCheckoutModalOpen', false)
            ->call('addToCart', $this->product->id)
            ->call('openCheckoutModal')
            ->assertSet('isCheckoutModalOpen', true)
            ->call('closeCheckoutModal')
            ->assertSet('isCheckoutModalOpen', false);
    }

    /** Full checkout flow dari cart sampai transaksi berhasil */
    public function test_pos_terminal_full_checkout(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->call('updateQuantity', $this->product->id, 2)
            ->set('customer_name', 'Pelanggan Test')
            ->set('payment_method', 'cash')
            ->set('paid_amount', 50000)
            ->call('checkout')
            ->assertDispatched('play-success-sound')
            ->assertCount('cart', 0);

        // Stok harus berkurang dari 10 menjadi 8
        $this->assertEquals(8, $this->product->fresh()->stock);
    }

    /** Kasir diarahkan ke /pos saat mencoba akses /admin */
    public function test_kasir_redirected_from_admin_to_pos(): void
    {
        $this->actingAs($this->kasir);

        $response = $this->get('/admin');

        // Kasir-only user harus di-redirect ke /pos
        $response->assertRedirect('/pos');
    }
}
