<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_customer_order_history_by_email(): void
    {
        $customer = Customer::factory()->create(['email' => 'alice@example.com']);
        $product = Product::factory()->create(['name' => 'Gadget']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'subtotal' => 100.00,
            'tax' => 10.00,
            'grand_total' => 110.00,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'line_subtotal' => 100.00,
            'line_tax' => 10.00,
            'line_total' => 110.00,
        ]);

        $response = $this->getJson('/api/customers/alice@example.com/orders');

        $response->assertStatus(200)
            ->assertJsonPath('data.customer.email', 'alice@example.com')
            ->assertJsonCount(1, 'data.orders')
            ->assertJsonPath('data.orders.0.id', $order->id)
            ->assertJsonPath('data.orders.0.order_items.0.product.name', 'Gadget');
    }

    public function test_returns_404_for_non_existent_customer_email(): void
    {
        $response = $this->getJson('/api/customers/nonexistent@example.com/orders');

        $response->assertStatus(404);
    }
}
