<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order with stock validation, calculations, and concurrency protection.
     *
     * @param array{name: string, email: string} $customerData
     * @param array<int, array{product_id: int, quantity: int}> $itemsData
     * @return Order
     * @throws InsufficientStockException
     */
    public function createOrder(array $customerData, array $itemsData): Order
    {
        return DB::transaction(function () use ($customerData, $itemsData) {
            // 1. Find or create customer by email
            $customer = Customer::firstOrCreate(
                ['email' => $customerData['email']],
                ['name' => $customerData['name']]
            );

            // Update customer name if it has changed
            if ($customer->name !== $customerData['name']) {
                $customer->update(['name' => $customerData['name']]);
            }

            // 2. Aggregate total requested quantities per product to handle duplicate product entries cleanly
            $aggregatedQuantities = [];
            foreach ($itemsData as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];
                $aggregatedQuantities[$productId] = ($aggregatedQuantities[$productId] ?? 0) + $quantity;
            }

            // Sort product IDs to prevent database deadlocks under high concurrent load
            $productIds = array_keys($aggregatedQuantities);
            sort($productIds);

            // 3. Lock target product database rows for update
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 4. Validate stock sufficiency for all items before making any modifications
            foreach ($aggregatedQuantities as $productId => $requiredQuantity) {
                /** @var Product|null $product */
                $product = $products->get($productId);

                if (! $product) {
                    throw new InsufficientStockException("Product with ID {$productId} was not found.");
                }

                if ($product->stock < $requiredQuantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for product '{$product->name}' (Requested: {$requiredQuantity}, Available: {$product->stock})."
                    );
                }
            }

            // 5. Calculate line items, subtotal, tax, and grand total & deduct stock
            $preparedOrderItems = [];
            $orderSubtotal = 0.00;
            $orderTax = 0.00;
            $orderGrandTotal = 0.00;

            foreach ($itemsData as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                /** @var Product $product */
                $product = $products->get($productId);

                $unitPrice = (float) $product->price;
                $taxPercentage = (float) $product->tax_percentage;

                $lineSubtotal = round($unitPrice * $quantity, 2);
                $lineTax = round(($lineSubtotal * $taxPercentage) / 100, 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $orderSubtotal += $lineSubtotal;
                $orderTax += $lineTax;
                $orderGrandTotal += $lineTotal;

                $preparedOrderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_percentage' => $taxPercentage,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ];
            }

            // Deduct stock for each locked product row
            foreach ($aggregatedQuantities as $productId => $requiredQuantity) {
                /** @var Product $product */
                $product = $products->get($productId);
                $product->decrement('stock', $requiredQuantity);
            }

            // 6. Create Order record
            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => round($orderSubtotal, 2),
                'tax' => round($orderTax, 2),
                'grand_total' => round($orderGrandTotal, 2),
            ]);

            // 7. Create OrderItems
            $order->orderItems()->createMany($preparedOrderItems);

            $order->load(['customer', 'orderItems.product']);

            // 8. Dispatch queued order confirmation email job (runs after transaction commit)
            SendOrderConfirmationJob::dispatch($order);

            return $order;
        });
    }
}
