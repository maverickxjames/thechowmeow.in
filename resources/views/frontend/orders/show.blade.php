@extends('layouts.app')
@section('title', 'Order ' . $order->order_number . ' — PetWear')

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('orders.index') }}" class="hover:text-violet-700 transition-colors">My Orders</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium font-mono">{{ $order->order_number }}</span>
        </nav>
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-gray-900 font-mono">{{ $order->order_number }}</h1>
            @php
                $statusConfig = [
                    'pending'   => 'bg-amber-50 text-amber-700 border-amber-100',
                    'paid'      => 'bg-blue-50 text-blue-700 border-blue-100',
                    'shipped'   => 'bg-violet-50 text-violet-700 border-violet-100',
                    'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                ];
                $statusCls = $statusConfig[$order->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
            @endphp
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold border {{ $statusCls }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <p class="text-sm text-gray-400 mt-1">Placed on {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('h:i A') }}</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="font-bold text-gray-900 text-base">Items Ordered</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 px-6 py-4">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->variant_info }}</p>
                                @if($item->sku)
                                    <p class="text-xs text-gray-300 mt-0.5 font-mono">SKU: {{ $item->sku }}</p>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs text-gray-400">Qty: {{ $item->quantity }}</p>
                                <p class="text-sm font-bold text-gray-900 mt-0.5">{{ currency_format($item->total) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Shipping Address --}}
            @if($order->shippingAddress)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 text-base mb-3">Shipping Address</h2>
                    <p class="text-sm font-semibold text-gray-900">{{ $order->shippingAddress->name }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $order->shippingAddress->phone }}</p>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $order->shippingAddress->full_address }}</p>
                </div>
            @endif
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-24">
                <h2 class="font-bold text-gray-900 text-base mb-4">Order Total</h2>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-900">{{ currency_format($order->subtotal) }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-emerald-700">
                            <span>Discount</span>
                            <span class="font-medium">−{{ currency_format($order->discount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        @if($order->shipping_cost == 0)
                            <span class="font-medium text-emerald-700">Free</span>
                        @else
                            <span class="font-medium text-gray-900">₹{{ number_format($order->shipping_cost) }}</span>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 pt-3 mt-1 flex justify-between">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-bold text-xl text-gray-900">{{ currency_format($order->total) }}</span>
                    </div>
                </div>

                @if($order->payment_method)
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Payment Method</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->payment_method) }}</p>
                    </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('orders.index') }}"
                       class="flex items-center justify-center gap-2 w-full border border-gray-200 text-gray-700 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
