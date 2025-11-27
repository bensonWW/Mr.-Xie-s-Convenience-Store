<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stats()
    {
        $totalSales = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $orderCount = Order::count();
        $userCount = User::where('role', 'customer')->count();
        $lowStockProducts = Product::where('stock', '<', 10)->get();

        // Recent orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Sales by category
        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(order_items.quantity * order_items.price) as total'))
            ->groupBy('products.category')
            ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'order_count' => $orderCount,
            'user_count' => $userCount,
            'low_stock_products' => $lowStockProducts,
            'recent_orders' => $recentOrders,
            'sales_by_category' => $salesByCategory
        ]);
    }

    public function users()
    {
        return User::all();
    }
}
