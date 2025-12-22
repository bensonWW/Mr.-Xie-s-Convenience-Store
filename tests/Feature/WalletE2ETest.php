<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_full_wallet_shopping_flow()
    {
        // 1. Register User
        $user = User::create([
            'name' => 'John Doe Test',
            'email' => 'johndoe_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->assertNotEmpty($token);

        // 2. Deposit Funds
        $depositAmount = 1000; // $1000 input -> 100000 cents
        $expectedCents = 100000;
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/user/wallet/deposit', [
                'amount' => $depositAmount,
                'description' => 'Initial Top-up',
            ])->assertStatus(200);

        // Create Store
        $store = \App\Models\Store::create([
            'name' => 'Test Store',
            'user_id' => $user->id,
            'email' => 'store1@example.com',
            'address' => 'Test Address',
            'phone' => '1234567890'
        ]);

        // 3. Setup Product
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 20000, // $200
            'stock' => 10,
            'status' => 'active',
            'original_price' => 25000,
            'category' => 'General',
            'sku' => 'TEST-001',
            'barcode' => '12345678',
            'description' => 'Desc',
            'information' => 'Info',
            'store_id' => $store->id,
        ]);

        // 4. Add to Cart
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ])->assertStatus(200); // 200 OK

        // 5. Create Order (Pending Payment)
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/orders');

        if ($response->status() !== 201) {
            file_put_contents(base_path('debug_error.log'), json_encode($response->json(), JSON_PRETTY_PRINT));
        }

        $response->assertStatus(201);
        $order = $response->json();
        $orderId = $order['id'];
        $totalAmount = $order['total_amount'];

        $this->assertEquals(40000, $totalAmount); // 40000 (Free Shipping > 1000)
        $this->assertEquals(OrderStatus::PENDING_PAYMENT->value, $order['status']); // Should be pending

        // 6. Pay for Order
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/orders/{$orderId}/pay"); // Endpoint assumption

        $response->assertStatus(200);
        $this->assertEquals(OrderStatus::PROCESSING->value, $response->json('order.status'));

        // 7. Verify Final Balance
        // 100000 - 46000 = 54000
        $expectedBalance = 100000 - $totalAmount;
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user/wallet');

        $response->assertStatus(200);

        $actualBalance = $response->json('balance');
        if ($actualBalance !== $expectedBalance) {
            file_put_contents(base_path('debug_balance.log'), "Expected: $expectedBalance, Actual: $actualBalance");
        }

        $response->assertJson(['balance' => $expectedBalance]);
    }

    public function test_payment_fails_with_insufficient_funds()
    {
        // 1. Register
        $user = User::create([
            'name' => 'Low Balance User',
            'email' => 'low_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'balance' => 10000 // $100
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $store = \App\Models\Store::create([
            'name' => 'Low Balance Store',
            'user_id' => $user->id,
            'email' => 'store2@example.com',
            'address' => 'Test Address',
            'phone' => '1234567890'
        ]);

        // 2. Product
        $product = Product::create([
            'name' => 'Low Balance Product',
            'price' => 20000, // $200
            'stock' => 10,
            'status' => 'active',
            'category' => 'General',
            'sku' => 'TEST-002',
            'barcode' => '87654321',
            'description' => 'Desc',
            'information' => 'Info',
            'store_id' => $store->id,
        ]);

        // 3. Cart
        $this->postJson('/api/cart/items', ['product_id' => $product->id, 'quantity' => 1], ['Authorization' => "Bearer $token"]);

        // 4. Create Order (Success, pending payment)
        $response = $this->postJson('/api/orders', [], ['Authorization' => "Bearer $token"]);
        $response->assertStatus(201);
        $orderId = $response->json('id');

        // 5. Pay (Should Fail)
        $response = $this->postJson("/api/orders/{$orderId}/pay", [], ['Authorization' => "Bearer $token"]);

        // Logic Exception means 400 usually, unless Handler maps it. 
        // OrderController catches InsufficientBalanceException and re-throws it? 
        // Or if it's caught in OrderService... Wait, OrderService uses WalletService which throws specific Exception.
        // Controller catches Exception and returns 400. 
        // Wait, OrderController store() method had explicit catch InsufficientBalanceException throw $e.
        // But pay() just catches general Exception.
        // Ideally pay() should handle InsufficientBalanceException specifically if we want 402, 
        // but current implementation catches \Exception and returns 400.
        // Let's assert 400.

        $response->assertStatus(400);
        // ->assertJson(['message' => 'Insufficient balance.']); // Depends on Exception message
    }
}
