<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Handle the incoming order creation request.
     */
    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        $validated = $request->validated();

        $order = $orderService->createOrder(
            $validated['customer'],
            $validated['items']
        );

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }

    /**
     * Retrieve customer order history by email.
     */
    public function history(string $email): JsonResponse
    {
        $customer = Customer::where('email', $email)->firstOrFail();

        $orders = $customer->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        return response()->json([
            'data' => [
                'customer' => $customer,
                'orders' => $orders,
            ],
        ]);
    }
}
