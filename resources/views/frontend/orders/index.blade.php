@extends('layouts.app')
@section('title', 'My Orders — ' . config('app.name', 'PetWear'))

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">My Orders</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if($orders->count())
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('orders.show', $order->order_number) }}"
                   class="group block bg-white rounded-xl border border-gray-100 shadow-sm hover:border-violet-200 hover:shadow-md transition-all duration-200 p-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-gray-900 font-mono">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-base font-bold text-gray-900">{{ currency_format($order->total) }}</p>
                            @php
                                $statusConfig = [
                                    'pending'   => ['bg-amber-50 text-amber-700 border-amber-100',   'Pending'],
                                    'paid'      => ['bg-blue-50 text-blue-700 border-blue-100',       'Paid'],
                                    'shipped'   => ['bg-violet-50 text-violet-700 border-violet-100', 'Shipped'],
                                    'delivered' => ['bg-emerald-50 text-emerald-700 border-emerald-100', 'Delivered'],
                                    'cancelled' => ['bg-red-50 text-red-600 border-red-100',          'Cancelled'],
                                ];
                                [$cls, $label] = $statusConfig[$order->status] ?? ['bg-gray-50 text-gray-600 border-gray-100', ucfirst($order->status)];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $cls }}">{{ $label }}</span>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @foreach($order->items->take(3) as $item)
                            <span class="text-xs bg-gray-50 text-gray-600 px-2.5 py-1 rounded-full border border-gray-100">{{ $item->product_name }}</span>
                        @endforeach
                        @if($order->items->count() > 3)
                            <span class="text-xs text-gray-400">+{{ $order->items->count() - 3 }} more</span>
                        @endif
                        <span class="ml-auto text-xs text-violet-600 font-medium group-hover:underline">View details →</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-sm text-gray-500 mb-8 max-w-xs">Start shopping and your orders will appear here.</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-violet-700 text-white font-semibold rounded-lg hover:bg-violet-800 transition-colors text-sm">
                Browse Products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    @endif
</div>
@endsection
