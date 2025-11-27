<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{id}', [StoreController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem']);

    // Orders
    Route::post('/coupons/check', [CouponController::class, 'check']);
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);

    // Profile
    Route::put('/profile', [ProfileController::class, 'update']);

    // Staff/Admin routes (should have middleware for role check, simplified here)
    Route::put('/stores/{id}', [StoreController::class, 'update']);
});
