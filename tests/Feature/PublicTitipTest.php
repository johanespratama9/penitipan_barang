<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\Consignor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTitipTest extends TestCase
{
    use RefreshDatabase;

    public function test_titip_page_can_be_rendered(): void
    {
        $response = $this->get('/titip');
        $response->assertStatus(200);
        $response->assertSee('Formulir Penitipan Barang');
    }

    public function test_public_user_can_submit_consignment(): void
    {
        $category = Category::create(['name' => 'Fashion', 'slug' => 'fashion']);

        $response = $this->post('/titip', [
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

        // Penitip baru otomatis dibuat
        $consignor = Consignor::where('phone', '087712345678')->first();
        $this->assertNotNull($consignor);
        $this->assertEquals('Rian Hidayat', $consignor->name);

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

