<?php

namespace Tests\Feature;

use App\Filament\Pages\Pos;
use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->cashier = User::firstOrCreate(
            ['email' => 'cashier_pos@test.com'],
            ['name' => 'Kasir POS', 'password' => bcrypt('password')]
        );
        $this->cashier->syncRoles(['kasir']);

        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $consignor = Consignor::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'consignor_id' => $consignor->id,
            'code' => 'PRD-00099',
            'barcode' => '899999999999',
            'name' => 'Keyboard Wireless',
            'selling_price' => 350000,
            'commission_type' => 'percentage',
            'commission_value' => 20,
            'stock' => 5,
            'status' => 'available',
        ]);
    }

    public function test_kasir_can_render_pos_page(): void
    {
        $this->actingAs($this->cashier);

        $response = $this->get('/admin/pos');
        $response->assertStatus(200);
        $response->assertSee('Scan Kamera HP');
    }

    public function test_pos_livewire_add_to_cart_and_scan(): void
    {
        $this->actingAs($this->cashier);

        Livewire::test(Pos::class)
            // Test scan barcode langsung (seperti dari kamera HP atau scanner)
            ->call('scanBarcodeDirect', '899999999999')
            ->assertCount('cart', 1)
            ->assertSet('cart.' . $this->product->id . '.quantity', 1)
            ->assertSet('cart.' . $this->product->id . '.price', 350000.0)
            // Tambah quantity
            ->call('updateQuantity', $this->product->id, 2)
            ->assertSet('cart.' . $this->product->id . '.quantity', 2)
            // Checkout
            ->set('customer_name', 'Pelanggan Toko')
            ->set('payment_method', 'cash')
            ->set('paid_amount', 700000)
            ->call('checkout')
            ->assertDispatched('play-success-sound')
            ->assertCount('cart', 0);

        // Pastikan stok berkurang dari 5 menjadi 3
        $this->assertEquals(3, $this->product->fresh()->stock);
    }
}

