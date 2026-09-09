<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fixed realistic sample products (varying prices, tax rates, and stock levels)
        Product::factory()->createMany([
            [
                'name' => 'Wireless Ergonomic Mouse',
                'code' => 'PRD-10001',
                'price' => 29.99,
                'tax_percentage' => 10.00,
                'stock' => 50,
            ],
            [
                'name' => 'Mechanical Gaming Keyboard',
                'code' => 'PRD-10002',
                'price' => 89.99,
                'tax_percentage' => 18.00,
                'stock' => 25,
            ],
            [
                'name' => 'Braided USB-C Cable 2m',
                'code' => 'PRD-10003',
                'price' => 12.50,
                'tax_percentage' => 5.00,
                'stock' => 3, // Low stock product
            ],
            [
                'name' => 'Dual Monitor Arm',
                'code' => 'PRD-10004',
                'price' => 45.00,
                'tax_percentage' => 10.00,
                'stock' => 1, // Low stock product
            ],
            [
                'name' => 'Leather Desk Mat',
                'code' => 'PRD-10005',
                'price' => 19.99,
                'tax_percentage' => 0.00,
                'stock' => 0, // Out of stock product
            ],
        ]);

        // Additional generated products
        Product::factory(8)->create();
        Product::factory(3)->lowStock()->create();
    }
}
