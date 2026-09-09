<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Retrieve all available products.
     */
    public function index(): JsonResponse
    {
        $products = Product::orderBy('name', 'asc')->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Retrieve products whose stock is at or below the low-stock threshold.
     */
    public function lowStock(Request $request): JsonResponse
    {
        $threshold = (int) $request->input(
            'threshold',
            config('inventory.low_stock_threshold')
        );

        $products = Product::where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json([
            'data' => [
                'threshold' => $threshold,
                'count' => $products->count(),
                'products' => $products,
            ],
        ]);
    }
}
