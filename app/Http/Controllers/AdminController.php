<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{


    public function stats()
    {
        return Cache::remember('admin_stats', 60, function () { // Reduced cache time for realtime feel
            // 1. Total Consumption (Actual Revenue)
            $totalSales = abs(\App\Models\WalletTransaction::where('type', 'payment')->sum('amount'));

            // 2. Counts
            $orderCount = Order::count();
            $userCount = User::where('role', 'customer')->count();

            // 3. Low Stock
            $lowStockProducts = Product::where('stock', '<', 10)->take(10)->get();

            // 4. Recent Orders
            $recentOrders = Order::with('user')->latest()->take(5)->get();

            // 5. Sales by Category (Keep OrderItems for granular product data)
            $salesByCategory = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('products.category', DB::raw('SUM(order_items.quantity * order_items.price) as total'))
                ->groupBy('products.category')
                ->get();

            // 6. AOV
            $aov = $orderCount > 0 ? round($totalSales / $orderCount) : 0;

            // 7. Top Products
            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('products.name', 'products.image', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.quantity * order_items.price) as total_amount'))
                ->groupBy('products.id', 'products.name', 'products.image')
                ->orderByDesc('total_qty')
                ->take(5)
                ->get();

            // 8. Last 7 Days Revenue Chart
            $chartData = [
                'labels' => [],
                'values' => []
            ];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $sum = \App\Models\WalletTransaction::where('type', 'payment')
                    ->whereDate('created_at', $date)
                    ->sum('amount');

                $chartData['labels'][] = now()->subDays($i)->format('m/d');
                $chartData['values'][] = abs($sum); // Convert negative payment to positive revenue
            }

            return [
                'total_sales' => $totalSales,
                'order_count' => $orderCount,
                'user_count' => $userCount,
                'low_stock_products' => $lowStockProducts,
                'recent_orders' => $recentOrders,
                'sales_by_category' => $salesByCategory,
                'aov' => $aov,
                'top_products' => $topProducts,
                'chart_data' => $chartData
            ];
        });
    }

    public function users(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum(['orders' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }], 'total_amount');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Logic for 'level' filtering involves aggregate sums which is complex in Eloquent without raw SQL.
        // For MVP Optimization, we might handle sorting by total_amount if requested, 
        // but exact level filtering on server side is defered to keep query simple efficient.

        return $query->latest()->paginate(15);
    }

    public function orders()
    {
        return Order::with('user')->latest()->paginate(20);
    }

    public function show($id)
    {
        return Order::with('items.product', 'user')->findOrFail($id);
    }

    public function showUser($id)
    {
        return User::with(['walletTransactions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);
    }

    public function walletTransaction(Request $request, $id, \App\Services\WalletService $walletService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:deposit,withdraw',
            'description' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);

        try {
            if ($request->type === 'deposit') {
                $walletService->deposit(
                    $user,
                    $request->amount,
                    $request->description,
                    'ADMIN_DEPOSIT_' . now()->timestamp
                );
            } else {
                $walletService->withdraw(
                    $user,
                    $request->amount,
                    $request->description,
                    'ADMIN_WITHDRAW_' . now()->timestamp
                );
            }
            return response()->json(['message' => 'Wallet updated successfully', 'balance' => $user->balance]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->role = 'customer'; // Default role, can be enhanced
        // $user->birthday = $request->birthday; // If migration exists
        // $user->gender = $request->gender; // If migration exists
        $user->save();

        return $user;
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
            'name' => 'required|string',
        ]);

        $user->email = $request->email;
        $user->name = $request->name;
        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('status')) $user->status = $request->status;
        if ($request->has('member_level')) $user->member_level = $request->member_level;
        if ($request->has('is_level_locked')) $user->is_level_locked = $request->boolean('is_level_locked');

        // Allow updating balance directly? No, use transaction method.

        $user->save();

        return $user;
    }
}
