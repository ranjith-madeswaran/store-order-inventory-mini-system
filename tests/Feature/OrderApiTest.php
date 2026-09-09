<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_successfully_with_single_product(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price' => 50.00,
            'tax_percentage' => 10.00,
            'stock' => 10,
        ]);

        $payload = [
            'customer' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Order created successfully.')
            ->assertJsonPath('data.subtotal', '100.00')
            ->assertJsonPath('data.tax', '10.00')
            ->assertJsonPath('data.grand_total', '110.00');

        // Verify Database Records
        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('orders', [
            'subtotal' => 100.00,
            'tax' => 10.00,
            'grand_total' => 110.00,
        ]);

        // Verify stock deduction
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);

        // Verify queued job dispatch
        Queue::assertPushed(SendOrderConfirmationJob::class, function ($job) {
            return $job->order->customer->email === 'john@example.com';
        });
    }

    public function test_can_create_order_with_multiple_products_and_correct_totals(): void
    {
        Queue::fake();

        $productA = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 10.00, // Subtotal: 200.00, Tax: 20.00, Line Total: 220.00
            'stock' => 10,
        ]);

        $productB = Product::factory()->create([
            'price' => 50.00,
            'tax_percentage' => 5.00, // Subtotal: 50.00, Tax: 2.50, Line Total: 52.50
            'stock' => 5,
        ]);

        $payload = [
            'customer' => [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
            ],
            'items' => [
                ['product_id' => $productA->id, 'quantity' => 2],
                ['product_id' => $productB->id, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.subtotal', '250.00')
            ->assertJsonPath('data.tax', '22.50')
            ->assertJsonPath('data.grand_total', '272.50');

        // Verify Stock Deductions
        $this->assertEquals(8, $productA->fresh()->stock);
        $this->assertEquals(4, $productB->fresh()->stock);

        Queue::assertPushed(SendOrderConfirmationJob::class);
    }

    public function test_fails_when_stock_is_insufficient(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price' => 20.00,
            'stock' => 2,
        ]);

        $payload = [
            'customer' => [
                'name' => 'Bob Jones',
                'email' => 'bob@example.com',
            ],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);

        // Stock should remain untouched
        $this->assertEquals(2, $product->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);

        Queue::assertNotPushed(SendOrderConfirmationJob::class);
    }

    public function test_fails_validation_for_invalid_quantity(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $payload = [
            'customer' => [
                'name' => 'Alice',
                'email' => 'alice@example.com',
            ],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 0],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    }

    public function test_fails_validation_for_invalid_product_id(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Alice',
                'email' => 'alice@example.com',
            ],
            'items' => [
                ['product_id' => 999999, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id']);
    }

    public function test_fails_validation_for_missing_customer_fields(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $payload = [
            'customer' => [
                'name' => '',
                'email' => 'not-an-email',
            ],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer.name', 'customer.email']);
    }

    public function test_concurrency_only_one_order_succeeds_when_last_item_is_purchased(): void
    {
        Queue::fake();

        // Product with exactly 1 item in stock
        $product = Product::factory()->create([
            'price' => 100.00,
            'tax_percentage' => 10.00,
            'stock' => 1,
        ]);

        $payloadBuyer1 = [
            'customer' => ['name' => 'First Buyer', 'email' => 'buyer1@example.com'],
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        $payloadBuyer2 = [
            'customer' => ['name' => 'Second Buyer', 'email' => 'buyer2@example.com'],
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        // Request 1 attempts to purchase the final unit
        $response1 = $this->postJson('/api/orders', $payloadBuyer1);

        // Request 2 attempts to purchase the final unit concurrently
        $response2 = $this->postJson('/api/orders', $payloadBuyer2);

        // 1. One order succeeds (201 Created) and one order fails cleanly (422 Unprocessable Entity)
        $response1->assertStatus(201);
        $response2->assertStatus(422)
            ->assertJsonPath('message', "Insufficient stock for product '{$product->name}' (Requested: 1, Available: 0).");

        // 2. Exactly one order exists in the database
        $this->assertDatabaseCount('orders', 1);

        // 3. Stock never becomes negative (final stock is exactly 0)
        $this->assertEquals(0, $product->fresh()->stock);

        // 4. Failed transaction created no partial order items
        $this->assertDatabaseCount('order_items', 1);

        // 5. Confirmation email job dispatched only once for the successful order
        Queue::assertPushed(SendOrderConfirmationJob::class, 1);
    }
}
