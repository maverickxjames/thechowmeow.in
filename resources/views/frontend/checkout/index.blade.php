@extends('layouts.app')
@section('title', 'Checkout — ' . config('app.name', 'PetWear'))

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('cart.index') }}" class="hover:text-violet-700 transition-colors">Cart</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">Checkout</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-center gap-2 mb-2 text-red-800 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Please fix the following errors:
                </div>
                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left: Shipping + Payment --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Saved Addresses --}}
                @if($addresses->count())
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-base">Saved Addresses</h3>
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                                <label class="flex items-start gap-3.5 p-4 rounded-lg border-2 cursor-pointer transition-all hover:border-violet-300
                                    {{ $address->is_default ? 'border-violet-500 bg-violet-50/50' : 'border-gray-100 hover:bg-gray-50' }}">
                                    <input type="radio" name="shipping_address_id" value="{{ $address->id }}"
                                           {{ $address->is_default ? 'checked' : '' }}
                                           class="mt-0.5 text-violet-600 focus:ring-violet-400 shrink-0">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $address->name }}
                                            <span class="text-gray-400 font-normal ml-2">{{ $address->phone }}</span>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-0.5 leading-relaxed">{{ $address->full_address }}</p>
                                        @if($address->is_default)
                                            <span class="text-[10px] font-semibold bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full mt-1.5 inline-block">Default</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- New Address --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6"
                     x-data="{ showNew: {{ $addresses->count() ? 'false' : 'true' }} }">
                    @if($addresses->count())
                        <button type="button" @click="showNew = !showNew"
                                class="flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-900 transition-colors mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span x-text="showNew ? 'Hide form' : 'Add a new address'"></span>
                        </button>
                    @else
                        <h3 class="font-bold text-gray-900 mb-4 text-base">Shipping Address</h3>
                    @endif

                    <div x-show="showNew" x-cloak x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">Full Name</label>
                            <input type="text" name="new_address[name]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.name', auth()->user()->name) }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">Phone</label>
                            <input type="text" name="new_address[phone]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.phone', auth()->user()->phone) }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">Address Line 1</label>
                            <input type="text" name="new_address[address_line_1]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.address_line_1') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">Address Line 2 <span class="font-normal text-gray-400">(optional)</span></label>
                            <input type="text" name="new_address[address_line_2]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.address_line_2') }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">City</label>
                            <input type="text" name="new_address[city]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.city') }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">State</label>
                            <input type="text" name="new_address[state]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.state') }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">PIN Code</label>
                            <input type="text" name="new_address[zip]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.zip') }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 block">Country</label>
                            <input type="text" name="new_address[country]"
                                   class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm"
                                   value="{{ old('new_address.country', 'India') }}">
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4 text-base">Payment Method</h3>
                    
                    @php
                        $codEnabled = config('petwear.enable_cod', true);
                        $razorpayEnabled = config('petwear.enable_razorpay', true);
                    @endphp

                    @if(!$codEnabled && !$razorpayEnabled)
                        <div class="p-4 bg-red-50 text-red-600 rounded-lg text-sm font-semibold">
                            No payment methods are currently available. Please contact support.
                        </div>
                    @else
                        <div class="space-y-3">
                            @if($codEnabled)
                            <label class="flex items-center gap-3.5 p-4 rounded-lg border-2 border-violet-500 bg-violet-50/50 cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" {{ !$razorpayEnabled ? 'checked' : 'checked' }}
                                       class="text-violet-600 focus:ring-violet-400">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Cash on Delivery</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pay when your order arrives</p>
                                </div>
                            </label>
                            @endif

                            @if($razorpayEnabled)
                            <label class="flex items-center gap-3.5 p-4 rounded-lg border-2 border-gray-100 cursor-pointer hover:border-gray-200 transition-colors"
                                @if(!$codEnabled) style="border-color:#8b5cf6; background-color:#f5f3ff;" @endif>
                                <input type="radio" name="payment_method" value="razorpay" 
                                       {{ !$codEnabled ? 'checked' : '' }}
                                       class="text-violet-600 focus:ring-violet-400">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Online Payment</p>
                                    <p class="text-xs text-gray-500 mt-0.5">UPI, Cards, Net Banking via Razorpay</p>
                                </div>
                            </label>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-3 text-base">Order Notes <span class="text-sm font-normal text-gray-400">(optional)</span></h3>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm resize-none"
                              placeholder="Special instructions for your order..."></textarea>
                </div>
            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-5 text-base">Order Summary</h3>

                    {{-- Items --}}
                    <div class="space-y-3 mb-5">
                        @foreach($cart->items as $item)
                            <div class="flex gap-3 items-center">
                                <div class="relative shrink-0">
                                    <img src="{{ $item->variant->product->primary_image_url }}"
                                         class="w-12 h-12 rounded-lg object-cover bg-gray-50">
                                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-700 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->variant->product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->variant->variant_label }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 shrink-0">{{ currency_format($item->subtotal) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">{{ currency_format($cart->total) }}</span>
                        </div>
                        @if($coupon)
                            <div class="flex justify-between text-emerald-700">
                                <span>Discount ({{ $coupon['code'] }})</span>
                                <span class="font-medium">−{{ currency_format($coupon['discount']) }}</span>
                            </div>
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
                                <span class="text-gray-600">Shipping</span>
                                @if($totalWeight > 0)
                                    <span class="text-[10px] text-gray-400">Total weight: {{ number_format($totalWeight, 2) }} kg</span>
                                @endif
                                <span class="text-[10px] text-violet-600 uppercase font-bold tracking-tighter">{{ $shippingType }}</span>
                            </div>
                            @if($shippingCost == 0)
                                <span class="font-medium text-emerald-700">Free</span>
                            @else
                                <span class="font-medium text-gray-900">{{ currency_format($shippingCost) }}</span>
                            @endif
                        </div>
                        <div class="border-t border-gray-100 pt-3 mt-1 flex justify-between">
                            @php
                                $discount = $coupon['discount'] ?? 0;
                                $total = $cart->total - $discount + $shippingCost;
                            @endphp
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="font-bold text-xl text-gray-900">{{ currency_format($total) }}</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-6 w-full bg-violet-700 text-white font-semibold py-3.5 rounded-lg hover:bg-violet-800 transition-colors flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Place Order
                    </button>

                    <div class="mt-4 flex items-center justify-center gap-1.5 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Your information is secure
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
