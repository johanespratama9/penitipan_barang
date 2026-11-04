<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuDigitalTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_digital_page_can_be_accessed_publicly(): void
    {
        $response = $this->get('/menu');
        $response->assertStatus(200);
        $response->assertSee('SARINAH STREET');
        $response->assertSee('Buku Menu Digital');
        $response->assertSee('Katalog Menu Digital');
        // Pastikan ada pesan bahwa halaman hanya untuk tampilan, bukan untuk pemesanan online
        $response->assertSee('hanya untuk menampilkan menu');
        $response->assertDontSee('Checkout');
        $response->assertDontSee('Tambah ke Keranjang');
    }

    public function test_menu_digital_displays_table_number_when_scanned_with_table_param(): void
    {
        $response = $this->get('/menu?meja=05');
        $response->assertStatus(200);
        $response->assertSee('05');
        $response->assertSee('Meja 05');
    }

    public function test_menu_digital_displays_available_products(): void
    {
        $consignor = \App\Models\Consignor::create([
            'name' => 'Mitra Kopi',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Minuman Kopi',
            'slug' => 'minuman-kopi',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Kopi Susu Gula Aren',
            'code' => 'PRD-99991',
            'category_id' => $category->id,
            'consignor_id' => $consignor->id,
            'purchase_price' => 10000,
            'selling_price' => 20000,
            'stock' => 15,
            'status' => 'available',
        ]);

        $response = $this->get('/menu');
        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('Minuman Kopi');
        $response->assertSee('Rp 20.000');
    }

    public function test_admin_can_access_table_barcode_print_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create([
            'email' => 'admin_barcode@example.com',
        ]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/dasbor/cetak-barcode-meja');
        $response->assertStatus(200);
        $response->assertSee('Cetak Barcode & QR Code Meja');
        $response->assertSee('Cetak Semua Barcode Meja');
        $response->assertSee('GARIS POTONG');
    }
}
