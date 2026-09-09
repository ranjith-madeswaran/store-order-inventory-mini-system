<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowStockApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_low_stock_products_using_default_config_threshold(): void
    {
        // Default threshold in config is 5
        $productLow1 = Product::factory()->create(['name' => 'Low Stock 1', 'stock' => 2]);
        $productLow2 = Product::factory()->create(['name' => 'Low Stock 2', 'stock' => 5]);
        $productNormal = Product::factory()->create(['name' => 'Normal Stock', 'stock' => 15]);

        $response = $this->getJson('/api/products/low-stock');

        $response->assertStatus(200)
            ->assertJsonPath('data.threshold', 5)
            ->assertJsonCount(2, 'data.products')
            ->assertJsonPath('data.products.0.id', $productLow1->id)
            ->assertJsonPath('data.products.1.id', $productLow2->id);
    }

    public function test_can_fetch_low_stock_products_using_query_param_override(): void
    {
        Product::factory()->create(['name' => 'Stock 1', 'stock' => 1]);
        Product::factory()->create(['name' => 'Stock 4', 'stock' => 4]);
        Product::factory()->create(['name' => 'Stock 10', 'stock' => 10]);

        $response = $this->getJson('/api/products/low-stock?threshold=2');

        $response->assertStatus(200)
            ->assertJsonPath('data.threshold', 2)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.stock', 1);
    }
}
