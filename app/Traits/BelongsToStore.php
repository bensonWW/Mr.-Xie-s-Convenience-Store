<?php

namespace App\Traits;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BelongsToStore trait for multi-tenant data isolation.
 * 
 * Apply this trait to any model that should be scoped to a store.
 * 
 * Usage:
 * 1. Add 'store_id' column to the model's table
 * 2. Use this trait in the model
 * 3. Call Store::setCurrentStore($store) to set the tenant context
 */
trait BelongsToStore
{
    /**
     * Current store context for multi-tenant queries.
     */
    protected static ?Store $currentStore = null;

    /**
     * Set the current store context.
     */
    public static function setCurrentStore(?Store $store): void
    {
        static::$currentStore = $store;
    }

    /**
     * Get the current store context.
     */
    public static function getCurrentStore(): ?Store
    {
        return static::$currentStore;
    }

    /**
     * Boot the trait - add global scope for store filtering.
     */
    protected static function bootBelongsToStore(): void
    {
        // Automatically filter by current store when set
        static::addGlobalScope('store', function (Builder $builder) {
            if (static::$currentStore) {
                $builder->where('store_id', static::$currentStore->id);
            }
        });

        // Automatically set store_id when creating
        static::creating(function ($model) {
            if (static::$currentStore && !$model->store_id) {
                $model->store_id = static::$currentStore->id;
            }
        });
    }

    /**
     * Define the store relationship.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope to query without store filtering (admin use).
     */
    public function scopeWithoutStoreScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('store');
    }

    /**
     * Scope to query for a specific store.
     */
    public function scopeForStore(Builder $query, int $storeId): Builder
    {
        return $query->withoutGlobalScope('store')->where('store_id', $storeId);
    }
}
