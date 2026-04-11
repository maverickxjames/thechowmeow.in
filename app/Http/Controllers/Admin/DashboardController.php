<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sales' => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('is_admin', false)->count(),
            'low_stock_count' => ProductVariant::active()->lowStock()->count(),
            'out_of_stock_count' => ProductVariant::active()->where('stock_quantity', '<=', 0)->count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(10)->get();
        $lowStockProducts = ProductVariant::with('product')
            ->active()
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
}
