<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Lookup customer details by email address.
     */
    public function lookup(Request $request): JsonResponse
    {
        $email = $request->query('email');

        if (! $email) {
            return response()->json(['data' => null]);
        }

        $customer = Customer::where('email', $email)->first();

        return response()->json([
            'data' => $customer,
        ]);
    }
}
