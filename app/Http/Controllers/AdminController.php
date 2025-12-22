<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function stats()
    {
        return Cache::remember('admin_stats', 60, function () {
            // 1. Total Consumption (Actual Revenue)
            $totalSales = Order::whereIn('status', ['processing', 'shipped', 'completed'])->sum('total_amount');

            // 2. Counts
            $orderCount = Order::count();
            $userCount = User::where('role', 'customer')->count();

            // 3. Low Stock
            $lowStockProducts = Product::where('stock', '<', 10)->take(10)->get();

            // 4. Recent Orders
            $recentOrders = Order::with('user')->latest()->take(5)->get();

            // 5. Sales by Category (using normalized categories table)
            $salesByCategory = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name as category', DB::raw('SUM(order_items.quantity * order_items.price) as total'))
                ->groupBy('categories.id', 'categories.name')
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

            // 8. Last 7 Days Revenue Chart (Optimized)
            $chartData = [
                'labels' => [],
                'values' => []
            ];

            // Use DATE() to group by day. 
            // Note: SQLite might need strftime('%Y-%m-%d', created_at), MySQL uses DATE(created_at).
            // Assuming MySQL or compatible for this "Production" grade code. 
            // If SQLite is used for testing, we might need a conditional or raw logic, 
            // but the prompt implies a serious environment (Scalability). MySQL is standard.
            $sevenDaysStats = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->whereIn('status', ['processing', 'shipped', 'completed'])
                ->groupBy('date')
                ->get()
                ->keyBy('date'); // Collection keyed by date string

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $chartData['labels'][] = now()->subDays($i)->format('m/d');

                // keyBy might produce '2025-12-22', so we access it directly.
                // However, selectRaw return structure might depend on driver. 
                // In MySQL: 'date' attribute is string 'YYYY-MM-DD'.
                $val = isset($sevenDaysStats[$date]) ? $sevenDaysStats[$date]->total : 0;
                $chartData['values'][] = $val;
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
}
