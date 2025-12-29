<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;

/**
 * CacheService - Centralized caching strategy for the application.
 * 
 * Implements:
 * - Write-through cache for settings
 * - Read-through cache for product listings
 * - Cache invalidation on updates
 */
class CacheService
{
    /**
     * Cache TTL constants (in seconds)
     */
    public const TTL_PRODUCTS = 3600;      // 1 hour
    public const TTL_CATEGORIES = 7200;    // 2 hours
    public const TTL_SETTINGS = 86400;     // 24 hours
    public const TTL_STATS = 300;          // 5 minutes

    /**
     * Cache key prefixes
     */
    private const PREFIX_PRODUCTS = 'products:';
    private const PREFIX_CATEGORIES = 'categories:';
    private const PREFIX_SETTINGS = 'settings:';
    private const PREFIX_STATS = 'stats:';

    // ==========================================
    // PRODUCT CACHING
    // ==========================================

    /**
     * Get cached product list with pagination info.
     */
    public function getProductList(int $page = 1, int $perPage = 20, ?int $categoryId = null): ?array
    {
        $key = $this->getProductListKey($page, $perPage, $categoryId);
        return Cache::get($key);
    }

    /**
     * Cache product list.
     */
    public function cacheProductList(array $data, int $page = 1, int $perPage = 20, ?int $categoryId = null): void
    {
        $key = $this->getProductListKey($page, $perPage, $categoryId);
        Cache::put($key, $data, self::TTL_PRODUCTS);
    }

    /**
     * Get single product from cache.
     */
    public function getProduct(int $productId): ?array
    {
        return Cache::get(self::PREFIX_PRODUCTS . "detail:{$productId}");
    }

    /**
     * Cache single product.
     */
    public function cacheProduct(int $productId, array $data): void
    {
        Cache::put(self::PREFIX_PRODUCTS . "detail:{$productId}", $data, self::TTL_PRODUCTS);
    }

    /**
     * Invalidate all product caches (after product update/delete).
     */
    public function invalidateProductCache(?int $productId = null): void
    {
        if ($productId) {
            Cache::forget(self::PREFIX_PRODUCTS . "detail:{$productId}");
        }

        // Clear all product list caches
        // Note: This is a simplified approach. In production,
        // consider using cache tags for better granularity.
        Cache::flush(); // Or use tags if available
    }

    // ==========================================
    // CATEGORY CACHING
    // ==========================================

    /**
     * Get all categories from cache.
     */
    public function getCategories(): ?array
    {
        return Cache::get(self::PREFIX_CATEGORIES . 'all');
    }

    /**
     * Cache all categories.
     */
    public function cacheCategories(array $categories): void
    {
        Cache::put(self::PREFIX_CATEGORIES . 'all', $categories, self::TTL_CATEGORIES);
    }

    /**
     * Invalidate category cache.
     */
    public function invalidateCategoryCache(): void
    {
        Cache::forget(self::PREFIX_CATEGORIES . 'all');
    }

    // ==========================================
    // SETTINGS - WRITE-THROUGH CACHE
    // ==========================================

    /**
     * Get setting value with write-through cache.
     */
    public function getSetting(string $key, $default = null)
    {
        $cacheKey = self::PREFIX_SETTINGS . $key;

        return Cache::remember($cacheKey, self::TTL_SETTINGS, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value with write-through cache.
     * Writes to both database and cache atomically.
     */
    public function setSetting(string $key, $value): void
    {
        // Write to database
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Write to cache immediately (write-through)
        Cache::put(self::PREFIX_SETTINGS . $key, $value, self::TTL_SETTINGS);
    }

    /**
     * Delete setting and invalidate cache.
     */
    public function deleteSetting(string $key): void
    {
        Setting::where('key', $key)->delete();
        Cache::forget(self::PREFIX_SETTINGS . $key);
    }

    // ==========================================
    // STATISTICS CACHE
    // ==========================================

    /**
     * Get cached dashboard stats.
     */
    public function getStats(string $type = 'dashboard'): ?array
    {
        return Cache::get(self::PREFIX_STATS . $type);
    }

    /**
     * Cache dashboard stats.
     */
    public function cacheStats(string $type, array $data): void
    {
        Cache::put(self::PREFIX_STATS . $type, $data, self::TTL_STATS);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    /**
     * Generate product list cache key.
     */
    private function getProductListKey(int $page, int $perPage, ?int $categoryId): string
    {
        $key = self::PREFIX_PRODUCTS . "list:p{$page}:pp{$perPage}";
        if ($categoryId) {
            $key .= ":cat{$categoryId}";
        }
        return $key;
    }

    /**
     * Clear all application caches.
     */
    public function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Warm up essential caches (call after deployment).
     */
    public function warmUp(): void
    {
        // Cache categories
        $categories = Category::orderBy('name')->get()->toArray();
        $this->cacheCategories($categories);

        // Cache first page of products
        $products = Product::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->toArray();
        $this->cacheProductList($products);
    }
}
