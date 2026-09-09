<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/low-stock', [ProductController::class, 'lowStock']);

Route::get('/customers/lookup', [CustomerController::class, 'lookup']);
Route::get('/customers/{email}/orders', [OrderController::class, 'history']);

Route::post('/orders', [OrderController::class, 'store']);
