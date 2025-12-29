<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Store model - Multi-tenant preparation.
 * 
 * Each store represents a separate tenant with isolated data.
 * 
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property array|null $settings
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'address',
        'phone',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Store statuses
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_PENDING = 'pending';

    /**
     * Get products for this store.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get members (users) for this store.
     */
    public function members()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get orders for this store.
     */
    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            User::class,
            'store_id', // Foreign key on users table
            'user_id',  // Foreign key on orders table
            'id',       // Local key on stores table
            'id'        // Local key on users table
        );
    }

    /**
     * Scope: Active stores only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get a setting value for this store.
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value for this store.
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Get the store's shipping fee configuration.
     */
    public function getShippingFee(): int
    {
        return $this->getSetting('shipping_fee', config('shipping.default_fee', 6000));
    }

    /**
     * Get the store's free shipping threshold.
     */
    public function getFreeShippingThreshold(): int
    {
        return $this->getSetting('free_shipping_threshold', config('shipping.free_shipping_threshold', 100000));
    }
}
