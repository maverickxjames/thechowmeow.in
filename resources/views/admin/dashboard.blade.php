@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', 'Welcome back! Here\'s what\'s happening.')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Sales</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">₹{{ number_format($stats['total_sales']) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">💰</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Orders</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">📦</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending Orders</p>
                <p class="text-2xl font-extrabold text-orange-500 mt-1">{{ $stats['pending_orders'] }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-2xl">⏳</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Low Stock Alerts</p>
                <p class="text-2xl font-extrabold text-red-500 mt-1">{{ $stats['low_stock_count'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-2xl">⚠️</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-purple-600 text-sm font-semibold hover:text-purple-700">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-3">Order</th><th class="pb-3">Customer</th><th class="pb-3">Total</th><th class="pb-3">Status</th></tr></thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-purple-600 font-medium hover:underline">{{ $order->order_number }}</a></td>
                            <td class="py-3">{{ $order->user->name ?? 'N/A' }}</td>
                            <td class="py-3 font-semibold">₹{{ number_format($order->total) }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                    @switch($order->status)
                                        @case('pending') bg-yellow-100 text-yellow-700 @break
                                        @case('paid') bg-blue-100 text-blue-700 @break
                                        @case('shipped') bg-purple-100 text-purple-700 @break
                                        @case('delivered') bg-green-100 text-green-700 @break
                                        @case('cancelled') bg-red-100 text-red-700 @break
                                    @endswitch
                                ">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Low Stock Alert --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Low Stock Products ⚠️</h3>
            <a href="{{ route('admin.inventory.index') }}" class="text-purple-600 text-sm font-semibold hover:text-purple-700">Manage →</a>
        </div>
        <div class="space-y-3">
            @forelse($lowStockProducts as $variant)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="font-medium text-sm text-gray-800">{{ $variant->product->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $variant->variant_label }} | SKU: {{ $variant->sku }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $variant->stock_quantity <= 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ $variant->stock_quantity }} left
                    </span>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-4">All stock levels are healthy! 🎉</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-2xl p-6 text-white">
        <p class="text-sm font-medium text-purple-100">Total Products</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['total_products'] }}</p>
    </div>
    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-2xl p-6 text-white">
        <p class="text-sm font-medium text-blue-100">Total Customers</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['total_customers'] }}</p>
    </div>
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-6 text-white">
        <p class="text-sm font-medium text-orange-100">Out of Stock</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['out_of_stock_count'] }}</p>
    </div>
</div>
@endsection
