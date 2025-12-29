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
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Public routes (with rate limiting for auth)
Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);
Route::get('/categories', [ProductController::class, 'categories']);

Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{id}', [StoreController::class, 'show']);
Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index']);

// Protected routes
Route::middleware(['auth:sanctum', 'refresh_token'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    // Email Verification (rate limited to prevent abuse)
    Route::middleware('throttle:email-verification')->group(function () {
        Route::post('/email/verification-notification', [VerificationController::class, 'send']);
        Route::post('/email/verify-code', [VerificationController::class, 'verifyCode']);
    });

    // Wallet (with rate limiting for financial operations)
    Route::middleware('throttle:api')->group(function () {
        Route::get('/user/wallet', [WalletController::class, 'show']);
        Route::post('/user/wallet/deposit', [WalletController::class, 'deposit']);
    });

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem']);

    // Coupons
    Route::post('/coupons/check', [CouponController::class, 'check']);
    Route::get('/coupons', [CouponController::class, 'index']);

    // Orders (payment operations with stricter rate limiting)
    Route::middleware('throttle:financial')->group(function () {
        Route::post('/orders/{order}/pay', [OrderController::class, 'pay']);
        Route::post('/orders/{order}/refund', [OrderController::class, 'refund']);
    });

    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);

    // Profile (handles structured address creation/update)
    Route::put('/profile', [ProfileController::class, 'update']);

    // Favorites
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index']);
    Route::post('/favorites', [App\Http\Controllers\FavoriteController::class, 'store']);
    Route::delete('/favorites/{productId}', [App\Http\Controllers\FavoriteController::class, 'destroy']);

    // Reviews
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
    Route::get('/products/{id}/reviews/can-review', [ReviewController::class, 'canReview']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Admin routes - accessible by both admin and staff
    Route::middleware(['is_admin', 'throttle:admin'])->prefix('admin')->group(function () {
        // Dashboard stats - staff can view
        Route::get('/stats', [App\Http\Controllers\AdminController::class, 'stats']);
        Route::get('/inventory-report', [App\Http\Controllers\AdminController::class, 'inventoryReport']);

        // Admin Order Management - staff can manage orders
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index']);
        Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show']);
        Route::post('/orders/{order}/refund', [App\Http\Controllers\Admin\OrderController::class, 'refund']);
        Route::put('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
        Route::put('/orders/{order}/logistics', [App\Http\Controllers\Admin\OrderController::class, 'updateLogistics']);

        // Admin Product Management - staff can manage products
        Route::get('/products', [ProductController::class, 'adminIndex']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Admin Product Variants Management - staff can manage variants
        Route::prefix('products/{product}')->group(function () {
            Route::get('/variants', [App\Http\Controllers\Admin\ProductVariantController::class, 'index']);
            Route::post('/variants', [App\Http\Controllers\Admin\ProductVariantController::class, 'storeVariant']);
            Route::post('/variants/bulk-generate', [App\Http\Controllers\Admin\ProductVariantController::class, 'bulkGenerate']);
            Route::put('/variants/bulk-price', [App\Http\Controllers\Admin\ProductVariantController::class, 'bulkUpdatePrice']);
            Route::put('/variants/bulk-stock', [App\Http\Controllers\Admin\ProductVariantController::class, 'bulkUpdateStock']);
            Route::post('/attributes', [App\Http\Controllers\Admin\ProductVariantController::class, 'storeAttribute']);
        });
        Route::put('/attributes/{attribute}', [App\Http\Controllers\Admin\ProductVariantController::class, 'updateAttribute']);
        Route::delete('/attributes/{attribute}', [App\Http\Controllers\Admin\ProductVariantController::class, 'destroyAttribute']);
        Route::post('/attributes/{attribute}/values', [App\Http\Controllers\Admin\ProductVariantController::class, 'storeAttributeValue']);
        Route::put('/attribute-values/{value}', [App\Http\Controllers\Admin\ProductVariantController::class, 'updateAttributeValue']);
        Route::delete('/attribute-values/{value}', [App\Http\Controllers\Admin\ProductVariantController::class, 'destroyAttributeValue']);
        Route::put('/variants/{variant}', [App\Http\Controllers\Admin\ProductVariantController::class, 'updateVariant']);
        Route::delete('/variants/{variant}', [App\Http\Controllers\Admin\ProductVariantController::class, 'destroyVariant']);
    });

    // Admin-only routes - only admin can access, not staff
    Route::middleware(['is_admin_only', 'throttle:admin'])->prefix('admin')->group(function () {
        // Coupon Management - admin only (with 7-day grace period for expired coupons)
        Route::get('/coupons', [CouponController::class, 'adminIndex']);
        Route::post('/coupons', [CouponController::class, 'store']);
        Route::put('/coupons/{id}', [CouponController::class, 'update']);
        Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);

        // Admin User Management - admin only
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::post('/users/{id}/wallet/transaction', [App\Http\Controllers\Admin\UserController::class, 'walletTransaction']);
        Route::put('/users/{id}/role', [App\Http\Controllers\Admin\UserController::class, 'updateRole']);
        Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
        Route::delete('/users/{id}/force', [App\Http\Controllers\Admin\UserController::class, 'forceDestroy']);

        // Admin Category Management - admin only
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index']);
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store']);
        Route::post('/categories/fix-slugs', [App\Http\Controllers\Admin\CategoryController::class, 'fixSlugs']);
        Route::put('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy']);
        Route::post('/categories/{id}/reassign', [App\Http\Controllers\Admin\CategoryController::class, 'reassignAndDelete']);

        // Store Management - admin only
        Route::put('/stores/{id}', [StoreController::class, 'update']);
    });
});
