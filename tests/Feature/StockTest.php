<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $consignor = Consignor::create(['name' => 'Budi', 'phone' => '08123456789']);

        $this->product = Product::create([
            'category_id' => $category->id,
            'consignor_id' => $consignor->id,
            'name' => 'Mouse Gaming',
            'selling_price' => 100000,
            'stock' => 5,
            'status' => 'available',
        ]);
    }

    public function test_stock_in_increases_product_stock(): void
    {
        $service = new StockService();
        $movement = $service->recordStockMovement($this->product, 3, 'in', 'consignment', 1, 'Penerimaan barang');

        $this->assertEquals(8, $this->product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 3,
        ]);
    }

    public function test_stock_out_decreases_product_stock(): void
    {
        $service = new StockService();
        $movement = $service->recordStockMovement($this->product, 2, 'out', 'sale', 1, 'Penjualan kasir');

        $this->assertEquals(3, $this->product->fresh()->stock);
        $this->assertEquals(2, $movement->quantity);
    }

    public function test_stock_out_fails_if_insufficient_stock(): void
    {
        $this->expectException(\Exception::class);

        $service = new StockService();
        $service->recordStockMovement($this->product, 10, 'out', 'sale', 1);
    }

    public function test_stock_adjustment(): void
    {
        $service = new StockService();
        $service->recordStockMovement($this->product, -2, 'adjustment', 'manual', null, 'Barang rusak');

        $this->assertEquals(3, $this->product->fresh()->stock);
    }
}

