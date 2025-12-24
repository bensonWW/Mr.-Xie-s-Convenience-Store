<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// Public routes (with rate limiting for auth)
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{id}', [StoreController::class, 'show']);
Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    // Wallet (with stricter rate limiting for financial operations)
        // Email Verification (code-based)
        Route::post('/email/verification-notification', [VerificationController::class, 'send']);
        Route::post('/email/verify-code', [VerificationController::class, 'verifyCode']);

    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/user/wallet', [WalletController::class, 'show']);
        Route::post('/user/wallet/deposit', [WalletController::class, 'deposit']);
    });

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem']);

    // Orders (payment operations with rate limiting)
    Route::post('/coupons/check', [CouponController::class, 'check']);
    Route::get('/coupons', [CouponController::class, 'index']);

    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/orders/{order}/pay', [OrderController::class, 'pay']);
        Route::post('/orders/{order}/refund', [OrderController::class, 'refund']);
    });

    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);

    // Profile
    Route::put('/profile', [ProfileController::class, 'update']);

    // Favorites
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index']);
    Route::post('/favorites', [App\Http\Controllers\FavoriteController::class, 'store']);
    Route::delete('/favorites/{productId}', [App\Http\Controllers\FavoriteController::class, 'destroy']);

    // Staff/Admin routes
    Route::middleware('is_admin')->prefix('admin')->group(function () {
        Route::apiResource('coupons', CouponController::class);
        Route::get('/stats', [App\Http\Controllers\AdminController::class, 'stats']);

        // Admin User Management
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::post('/users/{id}/wallet/transaction', [App\Http\Controllers\Admin\UserController::class, 'walletTransaction']);

        // Admin Order Management
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index']);
        Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show']);

        // Other Admin routes
        Route::post('/orders/{order}/refund', [App\Http\Controllers\OrderController::class, 'refund']); // This seems to still use the main OrderController, which is fine if logic is shared or acceptable.

        Route::get('/products', [ProductController::class, 'adminIndex']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
        Route::put('/orders/{order}/logistics', [OrderController::class, 'updateLogistics']);
        Route::put('/stores/{id}', [StoreController::class, 'update']);
    });
});

// Remove signed URL route (switched to code-based verification)
