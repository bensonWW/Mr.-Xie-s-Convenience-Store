<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress; // New
use App\Models\OrderSnapshot; // New
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\InsufficientBalanceException;
use App\Services\DTO\CreateOrderData;
use App\Enums\OrderStatus;

class OrderCreationService
{
    public function __construct(
        protected WalletService $walletService,
        protected InventoryService $inventoryService,
        protected PriceCalculator $priceCalculator,
        protected OrderSequenceGenerator $orderSequenceGenerator
    ) {}

    /**
     * Create an order for user.
     * 
     * @param CreateOrderData $data
     * @return Order
     * @throws Exception|InsufficientBalanceException
     */
    public function execute(CreateOrderData $data): Order
    {
        $user = $data->user;

        // 0. Preliminary Check (Fail Fast)
        $cart = Cart::where('user_id', $user->id)->with('items')->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw new Exception("Cart is empty");
        }

        // Optimization: Fetch Coupon outside transaction
        $coupon = null;
        if ($data->couponCode) {
            $coupon = Coupon::where('code', $data->couponCode)->first();
        }

        return DB::transaction(function () use ($user, $cart, $coupon, $data) {
            // 1. Re-fetch Cart INSIDE Lock
            $cartItems = $cart->items;
            if ($cartItems->isEmpty()) {
                throw new Exception("Cart is empty (checked inside transaction)");
            }

            // 2. Lock & Check Stock (Pessimistic Lock)
            $stockCheckItems = $cartItems->map(function ($item) {
                return ['product_id' => $item->product_id, 'quantity' => $item->quantity];
            })->toArray();

            $lockedStock = $this->inventoryService->lockAndCheckStock($stockCheckItems);

            // 3. Prepare Items for Price Calculation
            foreach ($cartItems as $item) {
                // Use LockedStock getter
                $item->setRelation('product', $lockedStock->get($item->product_id));
            }

            // 5. Calculate Price
            $priceResult = $this->priceCalculator->calculate($user, $cartItems, $coupon);

            // 6. Deduct Stock
            $this->inventoryService->deductStock($lockedStock, $stockCheckItems);

            // 7. Create Order Record
            $logisticsNumber = $this->orderSequenceGenerator->generateLogisticsNumber();

            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING_PAYMENT,
                'total_amount' => $priceResult->finalAmount,
                'discount_amount' => $priceResult->memberDiscount + $priceResult->couponDiscount,
                'shipping_fee' => $priceResult->shippingFee,
                'logistics_number' => $logisticsNumber,
                'payment_method' => $data->paymentMethod,
                // Removed shipping info and snapshot info from here
            ]);

            // 7b. Create Address
            OrderAddress::create([
                'order_id' => $order->id,
                'name' => $data->shippingName ?? $user->name,
                'phone' => $data->shippingPhone ?? $user->phone,
                'address' => $data->shippingAddress ?? $user->address,
            ]);

            // 7c. Create Snapshot (using atomic columns for 1NF compliance)
            OrderSnapshot::create([
                'order_id' => $order->id,
                'buyer_email' => $user->email,
                'member_level_name' => $user->member_level ?? 'normal',
                'user_name' => $user->name,
            ]);

            // 8. Create Order Items
            foreach ($priceResult->itemDetails as $detail) {
                $product = $lockedStock->get($detail['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $detail['product_id'],
                    'product_name' => $product->name,
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);
            }

            // 9. Clear Cart
            $cart->items()->delete();

            return $order->load(['items.product', 'address', 'snapshot']);
        });
    }
}
