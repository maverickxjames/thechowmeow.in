@extends('layouts.admin')
@section('title', 'Order: ' . $order->order_number)

@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-purple-600 hover:text-purple-700 text-sm font-semibold mb-4 inline-block">← Back to Orders</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Order Items --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4">Order Items</h3>
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-800">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->variant_info }} | SKU: {{ $item->sku }}</p>
                    </div>
                    <p class="text-sm">₹{{ number_format($item->price) }} × {{ $item->quantity }}</p>
                    <p class="font-bold text-sm">₹{{ number_format($item->total) }}</p>
                </div>
            @endforeach
        </div>

        {{-- Addresses --}}
        @if($order->shippingAddress)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-3">Shipping Address</h3>
                <p class="text-sm text-gray-600">{{ $order->shippingAddress->name }} | {{ $order->shippingAddress->phone }}</p>
                <p class="text-sm text-gray-600">{{ $order->shippingAddress->full_address }}</p>
            </div>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Summary --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4">Order Summary</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>₹{{ number_format($order->subtotal) }}</span></div>
                @if($order->discount > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>-₹{{ number_format($order->discount) }}</span></div>@endif
                <div class="flex justify-between"><span class="text-gray-500">Shipping</span><span>₹{{ number_format($order->shipping_cost) }}</span></div>
                <hr>
                <div class="flex justify-between text-lg"><span class="font-bold">Total</span><span class="font-extrabold text-purple-600">₹{{ number_format($order->total) }}</span></div>
            </div>
        </div>

        {{-- Status Update --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4">Update Status</h3>
            <p class="text-sm text-gray-500 mb-3">Current: <span class="font-bold text-gray-800">{{ ucfirst($order->status) }}</span></p>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex gap-2">
                @csrf @method('PUT')
                <select name="status" class="flex-1 rounded-lg border-gray-200 text-sm">
                    @foreach(['pending','paid','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700">Update</button>
            </form>
        </div>

        {{-- Customer Info --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-3">Customer</h3>
            <p class="text-sm text-gray-700 font-medium">{{ $order->user->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-500">{{ $order->user->email ?? '' }}</p>
            <p class="text-sm text-gray-500">{{ $order->user->phone ?? '' }}</p>
        </div>
    </div>
</div>
@endsection
