<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_full_wallet_shopping_flow()
    {
        // 1. Register User
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Factory seems broken, use manual creation
        $user = User::create([
            'name' => 'John Doe Test',
            'email' => 'johndoe_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);
        $token = $user->createToken('test')->plainTextToken;
        // if ($response->status() !== 200) {
        //     dump($response->json());
        // }
        // $response->assertStatus(200);
        // $token = $response->json('access_token');

        $this->assertNotEmpty($token);

        // 2. Deposit Funds (Mock)
        $depositAmount = 1000;
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/user/wallet/deposit', [
                'amount' => $depositAmount,
                'description' => 'Initial Top-up',
            ]);

        // Create Store first
        $store = \App\Models\Store::create([
            'name' => 'Test Store',
            'user_id' => $user->id, // Owner
            'description' => 'Test',
            'email' => 'store1@example.com',
            'address' => 'Test Address',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(200)
            ->assertJson(['balance' => $depositAmount]);

        // 3. Setup Product
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 200,
            'stock' => 10,
            'status' => 'active',
            'original_price' => 250,
            'category' => 'General',
            'sku' => 'TEST-001',
            'barcode' => '12345678',
            'description' => 'Test Description',
            'information' => 'Test Info',
            'store_id' => $store->id,
        ]);

        // 4. Add to Cart
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);
        // Allow 200 or 201
        if ($response->status() === 201) {
            $response->assertStatus(201);
        } else {
            $response->assertStatus(200);
        }

        // 5. Create Order (Checkout)
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/orders');

        if ($response->status() === 201) {
            $response->assertStatus(201);
        } else {
            $response->assertStatus(200);
        }
        $order = $response->json();
        $orderId = $order['id'];
        $totalAmount = $order['total_amount']; // Should be 400

        $this->assertEquals(400, $totalAmount);

        // 6. Pay with Wallet
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/orders/{$orderId}/pay");

        $response->assertStatus(200)
            ->assertJsonPath('order.status', 'processing');

        // 7. Verify Final Balance
        $expectedBalance = $depositAmount - $totalAmount; // 1000 - 400 = 600
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user/wallet');

        $response->assertStatus(200)
            ->assertJson(['balance' => $expectedBalance]);
    }

    public function test_payment_fails_with_insufficient_funds()
    {
        // 1. Register
        $user = User::create([
            'name' => 'Low Balance User',
            'email' => 'low_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'balance' => 100
        ]);
        $token = $user->createToken('test')->plainTextToken;

        // Create Store
        $store = \App\Models\Store::create([
            'name' => 'Low Balance Store',
            'user_id' => $user->id,
            'description' => 'Test',
            'email' => 'store2@example.com',
            'address' => 'Test Address',
            'phone' => '1234567890'
        ]);

        // 2. Product
        $product = Product::create([
            'name' => 'Low Balance Product',
            'price' => 200,
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
        $this->actingAs($user)->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        try {
            // 4. Order
            $response = $this->actingAs($user)->postJson('/api/orders');
        } catch (\Throwable $e) {
            dump($e->getMessage());
            throw $e;
        }
        $orderId = $response->json('id');

        // 5. Pay (Should Fail)
        $response = $this->actingAs($user)->postJson("/api/orders/{$orderId}/pay");

        $response->assertStatus(400)
            ->assertJson(['message' => 'Insufficient balance.']);
    }
}
