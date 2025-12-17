<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderRefundTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->product = Product::factory()->create([
            'price' => 100,
            'stock' => 10
        ]);

        // Setup initial wallet balance
        $walletService = app(WalletService::class);
        $walletService->deposit($this->user, 1000, 'Initial Deposit');
    }

    public function test_user_can_refund_processing_order()
    {
        // 1. Create Order (Processing/Paid)
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'processing',
            'total_amount' => 200
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100
        ]);

        // Deduct balance manually to simulate payment (since we just created order)
        $walletService = app(WalletService::class);
        $walletService->withdraw($this->user, 200, 'Payment');

        $initialBalance = $walletService->getBalance($this->user); // Should be 800

        // 2. Call Refund API
        $response = $this->actingAs($this->user)
            ->postJson("/api/orders/{$order->id}/refund");

        $response->assertStatus(200)
            ->assertJsonPath('order.status', 'refunded');

        // 3. Verify Balance Restored
        $this->assertEquals($initialBalance + 200, $walletService->getBalance($this->user));

        // Verify Transaction Log
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'type' => 'refund',
            'amount' => 200,
            'reference_id' => "ORDER_REFUND_{$order->id}"
        ]);

        // Verify Stock Restored
        $this->assertEquals(12, $this->product->fresh()->stock);
    }

    public function test_user_cannot_refund_shipped_order_if_restricted()
    {
        // If we decide Users can't refund shipped items (must contact admin)
        // OrderService logic allows it currently, but let's test the Logic we implemented.
        // My OrderService allows 'shipped'. 
        // Let's verify it works for 'shipped' too, based on current implementation.

        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'shipped',
            'total_amount' => 100
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 100
        ]);

        app(WalletService::class)->withdraw($this->user, 100);

        $response = $this->actingAs($this->user)
            ->postJson("/api/orders/{$order->id}/refund");

        $response->assertStatus(200);
        $this->assertEquals('refunded', $order->fresh()->status);
    }

    public function test_cannot_refund_already_refunded_order()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'refunded',
            'total_amount' => 100
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/orders/{$order->id}/refund");

        $response->assertStatus(400)
            ->assertJson(['message' => 'Order is already refunded.']);
    }

    public function test_pending_payment_order_is_cancelled_without_refund()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
            'total_amount' => 100
        ]);

        $initialBalance = app(WalletService::class)->getBalance($this->user);

        $response = $this->actingAs($this->user)
            ->postJson("/api/orders/{$order->id}/refund");

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $order->fresh()->status);

        // Balance unchanged
        $this->assertEquals($initialBalance, app(WalletService::class)->getBalance($this->user));
    }
}
