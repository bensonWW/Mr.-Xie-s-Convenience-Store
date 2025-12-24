<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MemberLevel;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\OrderCreationService;
use App\Services\DTO\CreateOrderData;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderCreationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderCreationService::class);

        // Ensure 'normal' level exists for MemberLevelService fallback
        $this->getMemberLevel('normal');
    }

    protected function getMemberLevel(string $slug): MemberLevel
    {
        return MemberLevel::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => ucfirst($slug) . ' 會員',
                'threshold' => match ($slug) {
                    'normal' => 0,
                    'vip' => 1000,
                    'gold' => 3000,
                    'platinum' => 5000,
                    'diamond' => 10000,
                    default => 0,
                },
                'discount' => match ($slug) {
                    'normal' => 0.00,
                    'vip' => 0.05,
                    'gold' => 0.08,
                    'platinum' => 0.10,
                    'diamond' => 0.15,
                    default => 0.00,
                },
            ]
        );
    }

    public function test_it_creates_order_successfully()
    {
        // 1. User & Wallet
        $goldLevel = $this->getMemberLevel('gold');
        $user = User::factory()->create([
            'balance' => 200000,
            'member_level_id' => $goldLevel->id,
        ]);
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Test Store 123',
            'email' => 'store123@example.com',
            'address' => 'Test Address',
            'phone' => '1234567890'
        ]);
        $product = Product::factory()->create([
            'store_id' => $store->id,
            'price' => 10000,
            'stock' => 10
        ]);

        // 2. Cart
        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // 3. Execute
        $data = new CreateOrderData(user: $user);
        $order = $this->service->execute($data);

        // 4. Verify
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => OrderStatus::PENDING_PAYMENT->value]);

        // Verify Address Table
        $this->assertDatabaseHas('order_addresses', [
            'order_id' => $order->id,
            'name' => $user->name,
        ]);

        // Verify Snapshot Table
        $this->assertDatabaseHas('order_snapshots', [
            'order_id' => $order->id,
            'buyer_email' => $user->email,
        ]);

        // Load relationship to check atomic columns
        $order->load('snapshot');
        $this->assertEquals('gold', $order->snapshot->member_level_name);

        // Cart cleared
        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
        // Stock reduced
        $this->assertEquals(8, $product->fresh()->stock);
        // Balance NOT deducted yet (pending_payment)
        $this->assertEquals(200000, $user->fresh()->balance);
    }

    public function test_snapshot_member_level_is_persisted()
    {
        $diamondLevel = $this->getMemberLevel('diamond');
        $normalLevel = $this->getMemberLevel('normal');

        $user = User::factory()->create([
            'balance' => 200000,
            'member_level_id' => $diamondLevel->id,
        ]);
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Snapshot Store',
            'email' => 'snap@example.com',
            'address' => 'Test Address',
            'phone' => '1234567890'
        ]);
        $product = Product::factory()->create(['store_id' => $store->id, 'price' => 10000, 'stock' => 10]);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $data = new CreateOrderData(user: $user);
        $order = $this->service->execute($data);

        $order->load('snapshot');
        $this->assertEquals('diamond', $order->snapshot->member_level_name);

        // Change user level
        $user->update(['member_level_id' => $normalLevel->id]);

        // Order shouldn't follow
        $this->assertEquals('diamond', $order->fresh()->snapshot->member_level_name);
    }
}
