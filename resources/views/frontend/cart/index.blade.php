@extends('layouts.app')
@section('title', 'Shopping Cart — ' . config('app.name', 'PetWear'))

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">Shopping Cart</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if($cart && $cart->items->count())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart->items as $item)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex gap-4 items-center">
                        <a href="{{ route('products.show', $item->variant->product->slug) }}" class="shrink-0">
                            <img src="{{ $item->variant->product->primary_image_url }}"
                                 alt="{{ $item->variant->product->name }}"
                                 class="w-20 h-20 rounded-lg object-cover bg-gray-50">
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->variant->product->slug) }}"
                               class="font-semibold text-gray-900 hover:text-violet-700 text-sm block truncate transition-colors">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item->variant->variant_label }}</p>
                            <p class="text-sm font-bold text-gray-900 mt-1.5">{{ currency_format($item->variant->effective_price) }}</p>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <form action="{{ route('cart.update') }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                    <button type="submit" name="quantity" value="{{ max(0, $item->quantity - 1) }}"
                                            class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors text-sm font-bold">−</button>
                                    <span class="w-9 h-8 flex items-center justify-center text-sm font-semibold bg-white border-x border-gray-200">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ min(10, $item->quantity + 1) }}"
                                            class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors text-sm font-bold">+</button>
                                </div>
                            </form>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900 min-w-[4rem] text-right">{{ currency_format($item->subtotal) }}</p>
                                <form action="{{ route('cart.remove') }}" method="POST" class="mt-1">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                    <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition-colors">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-2">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm text-violet-700 hover:text-violet-900 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                        Continue Shopping
                    </a>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-5 text-base">Order Summary</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal <span class="text-gray-400">({{ $cart->item_count }} {{ Str::plural('item', $cart->item_count) }})</span></span>
                            <span class="font-semibold text-gray-900">{{ currency_format($cart->total) }}</span>
                        </div>

                        @if($coupon)
                            <div class="flex justify-between text-emerald-700">
                                <span>Discount
                                    <span class="text-xs font-semibold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full ml-1">{{ $coupon['code'] }}</span>
                                </span>
                                <span class="font-semibold">−₹{{ number_format($coupon['discount']) }}</span>
                            </div>
                            <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">Remove coupon</button>
                            </form>
                        @endif

                        @php
                            $shippingService = app(\App\Services\ShippingService::class);
                            $shippingType = $shippingService->detectShippingType();
                            $shippingCost = $shippingService->calculateShipping($cart, $shippingType);
                            
                            $totalWeight = 0;
                            foreach($cart->items as $item) {
                                $totalWeight += ($item->variant->product->weight ?? 0) * $item->quantity;
                            }
                        @endphp

                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <span class="text-gray-500">Shipping</span>
                                @if($totalWeight > 0)
                                    <span class="text-[10px] text-gray-400">Total weight: {{ number_format($totalWeight, 2) }} kg</span>
                                @endif
                                <span class="text-[10px] text-violet-600 uppercase font-bold tracking-tighter">{{ $shippingType }}</span>
                            </div>
                            @if($shippingCost == 0)
                                <span class="font-semibold text-emerald-700">Free</span>
                            @else
                                <span class="font-semibold text-gray-900">{{ currency_format($shippingCost) }}</span>
                            @endif
                        </div>

                        @php
                            $threshold = (float) (App\Models\Setting::where('key', 'free_shipping_threshold')->value('value') ?? 0);
                        @endphp
                        @if($threshold > 0 && $cart->total < $threshold)
                            <p class="text-xs text-gray-400 bg-gray-50 rounded-lg px-3 py-2">
                                Add {{ currency_format($threshold - $cart->total) }} more for free shipping
                            </p>
                        @endif

                        <div class="border-t border-gray-100 pt-3 mt-1">
                            @php
                                $discount = $coupon['discount'] ?? 0;
                                $total = $cart->total - $discount + $shippingCost;
                            @endphp
                            <div class="flex justify-between">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-xl text-gray-900">{{ currency_format($total) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Coupon --}}
                    @if(!$coupon)
                        <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mt-5">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="code" placeholder="Coupon code"
                                       class="flex-1 rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none min-w-0" required>
                                <button type="submit"
                                        class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg font-semibold hover:bg-gray-800 transition-colors shrink-0">Apply</button>
                            </div>
                        </form>
                    @endif

                    <a href="{{ route('checkout.index') }}"
                       class="mt-5 flex items-center justify-center gap-2 w-full bg-violet-700 text-white font-semibold py-3.5 rounded-lg hover:bg-violet-800 transition-colors text-sm">
                        Proceed to Checkout
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    <div class="mt-4 flex items-center justify-center gap-1.5 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Secure & encrypted checkout
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
            <p class="text-sm text-gray-500 mb-8 max-w-xs">Looks like you haven't added anything yet. Explore our collection to find the perfect outfit for your pet.</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-violet-700 text-white font-semibold rounded-lg hover:bg-violet-800 transition-colors text-sm">
                Browse Products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    @endif
</div>
@endsection
