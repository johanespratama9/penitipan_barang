<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\Consignor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicTitipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_guest_is_redirected_to_register_page_when_visiting_titip(): void
    {
        $response = $this->get('/titip');
        $response->assertRedirect(route('penitip.register'));
        $response->assertSessionHas('info');
    }

    public function test_penitip_register_page_can_be_rendered(): void
    {
        $response = $this->get('/daftar-penitip');
        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran Mitra');
        $response->assertSee('Daftar Akun');
    }

    public function test_consignor_can_register_new_account_and_is_logged_in(): void
    {
        $response = $this->post('/daftar-penitip', [
            'name' => 'Budi Santoso',
            'phone' => '081298765432',
            'email' => 'budi@example.com',
            'address' => 'Bandung, Jawa Barat',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('titip.index'));
        $response->assertSessionHas('welcome_penitip');

        // User terbuat dan memiliki role penitip
        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('penitip'));

        // Consignor terbuat dan terhubung dengan user_id
        $consignor = Consignor::where('phone', '081298765432')->first();
        $this->assertNotNull($consignor);
        $this->assertEquals($user->id, $consignor->user_id);
        $this->assertEquals('Budi Santoso', $consignor->name);

        // User terautentikasi dalam sesi
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_penitip_can_view_titip_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
        ]);
        $user->assignRole('penitip');

        $consignor = Consignor::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '085512345678',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/titip');
        $response->assertStatus(200);
        $response->assertSee('Formulir Penitipan Barang');
        $response->assertSee('Siti Aminah');
        $response->assertSee('085512345678');
    }

    public function test_authenticated_penitip_can_submit_consignment(): void
    {
        $category = Category::create(['name' => 'Fashion', 'slug' => 'fashion']);

        $user = User::factory()->create([
            'name' => 'Rian Hidayat',
            'email' => 'rian@example.com',
        ]);
        $user->assignRole('penitip');

        $consignor = Consignor::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '087712345678',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/titip', [
            'consignor_name' => 'Rian Hidayat',
            'phone' => '087712345678',
            'email' => 'rian@example.com',
            'address' => 'Jakarta Selatan',
            'product_name' => 'Jam Tangan Seiko 5',
            'category_id' => $category->id,
            'quantity' => 1,
            'selling_price' => 1500000,
            'purchase_price' => 1200000,
            'description' => 'Original box lengkap',
        ]);

        $response->assertRedirect(route('titip.index'));
        $response->assertSessionHas('success');

        // Dokumen consignment terbuat dengan status submitted
        $consignment = Consignment::where('consignor_id', $consignor->id)->first();
        $this->assertNotNull($consignment);
        $this->assertEquals('submitted', $consignment->status);
        $this->assertEquals(1, $consignment->total_items);

        // Item konsinyasi berstatus pending
        $this->assertDatabaseHas('consignment_items', [
            'consignment_id' => $consignment->id,
            'product_name' => 'Jam Tangan Seiko 5',
            'selling_price' => 1500000,
            'status' => 'pending',
        ]);
    }
}
