<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Exception;

/**
 * RedisInventoryService - High-concurrency inventory management using Redis Lua scripts.
 * 
 * This service provides atomic stock operations using Redis Lua scripts,
 * designed for high-throughput scenarios (>500 TPS).
 * 
 * The database remains the source of truth; Redis acts as a fast lock/check layer.
 */
class RedisInventoryService
{
    /**
     * Redis key prefix for inventory.
     */
    private const KEY_PREFIX = 'inventory:stock:';

    /**
     * Lua script for atomic stock check and reserve.
     * Returns: 1 = success, 0 = insufficient stock, -1 = key not found
     */
    private const LUA_RESERVE_STOCK = <<<'LUA'
        local key = KEYS[1]
        local quantity = tonumber(ARGV[1])
        
        local current = redis.call('GET', key)
        if current == false then
            return -1
        end
        
        current = tonumber(current)
        if current < quantity then
            return 0
        end
        
        redis.call('DECRBY', key, quantity)
        return 1
    LUA;

    /**
     * Lua script for atomic batch stock check and reserve.
     * KEYS: product keys, ARGV: quantities (same order)
     * Returns: 1 = all success, 0 = one or more insufficient, product_id of failure
     */
    private const LUA_BATCH_RESERVE = <<<'LUA'
        local key_count = #KEYS
        
        -- First pass: check all stocks
        for i = 1, key_count do
            local current = redis.call('GET', KEYS[i])
            if current == false then
                return {-1, KEYS[i]}
            end
            
            if tonumber(current) < tonumber(ARGV[i]) then
                return {0, KEYS[i], tonumber(current), tonumber(ARGV[i])}
            end
        end
        
        -- Second pass: deduct all stocks atomically
        for i = 1, key_count do
            redis.call('DECRBY', KEYS[i], tonumber(ARGV[i]))
        end
        
        return {1}
    LUA;

    /**
     * Lua script for restoring stock (rollback/refund).
     */
    private const LUA_RESTORE_STOCK = <<<'LUA'
        local key = KEYS[1]
        local quantity = tonumber(ARGV[1])
        
        local current = redis.call('GET', key)
        if current == false then
            redis.call('SET', key, quantity)
        else
            redis.call('INCRBY', key, quantity)
        end
        
        return redis.call('GET', key)
    LUA;

    /**
     * Check if Redis inventory is enabled.
     */
    public function isEnabled(): bool
    {
        return config('inventory.redis_enabled', false);
    }

    /**
     * Get the Redis key for a product's stock.
     */
    private function getKey(int $productId): string
    {
        return self::KEY_PREFIX . $productId;
    }

    /**
     * Reserve stock for a single product atomically.
     * 
     * @throws Exception
     */
    public function reserveStock(int $productId, int $quantity): bool
    {
        $result = Redis::eval(
            self::LUA_RESERVE_STOCK,
            1,
            $this->getKey($productId),
            $quantity
        );

        if ($result === -1) {
            throw new Exception("Product ID {$productId} not found in Redis inventory cache.");
        }

        return $result === 1;
    }

    /**
     * Reserve stock for multiple products atomically (all-or-nothing).
     * 
     * @param array $items Array of ['product_id' => int, 'quantity' => int]
     * @return bool True if all reservations successful
     * @throws Exception
     */
    public function batchReserveStock(array $items): bool
    {
        if (empty($items)) {
            return true;
        }

        $keys = [];
        $quantities = [];

        foreach ($items as $item) {
            $keys[] = $this->getKey($item['product_id']);
            $quantities[] = $item['quantity'];
        }

        $result = Redis::eval(
            self::LUA_BATCH_RESERVE,
            count($keys),
            ...$keys,
            ...$quantities
        );

        if ($result[0] === -1) {
            throw new Exception("Product not found in Redis: {$result[1]}");
        }

        if ($result[0] === 0) {
            $productKey = $result[1];
            $available = $result[2] ?? 0;
            $requested = $result[3] ?? 0;
            throw new Exception(
                "Insufficient stock for product. Available: {$available}, Requested: {$requested}"
            );
        }

        return $result[0] === 1;
    }

    /**
     * Restore (add back) stock for a product.
     * Used for order cancellation, refunds, or rollbacks.
     */
    public function restoreStock(int $productId, int $quantity): int
    {
        return (int) Redis::eval(
            self::LUA_RESTORE_STOCK,
            1,
            $this->getKey($productId),
            $quantity
        );
    }

    /**
     * Sync product stock from database to Redis.
     */
    public function syncFromDatabase(int $productId, int $stock): void
    {
        Redis::set($this->getKey($productId), $stock);
    }

    /**
     * Sync multiple products from database to Redis.
     * 
     * @param array $products Array of ['id' => int, 'stock' => int]
     */
    public function batchSyncFromDatabase(array $products): void
    {
        $pipeline = Redis::pipeline();

        foreach ($products as $product) {
            $pipeline->set($this->getKey($product['id']), $product['stock']);
        }

        $pipeline->execute();
    }

    /**
     * Get current stock from Redis.
     */
    public function getStock(int $productId): ?int
    {
        $stock = Redis::get($this->getKey($productId));
        return $stock !== null ? (int) $stock : null;
    }

    /**
     * Clear a product's stock from Redis cache.
     */
    public function clearCache(int $productId): void
    {
        Redis::del($this->getKey($productId));
    }

    /**
     * Clear all inventory cache.
     */
    public function clearAllCache(): void
    {
        $keys = Redis::keys(self::KEY_PREFIX . '*');
        if (!empty($keys)) {
            Redis::del($keys);
        }
    }
}
