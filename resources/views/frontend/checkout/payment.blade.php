@extends('layouts.app')
@section('title', 'Complete Payment — ' . config('app.name', 'PetWear'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden p-8 text-center max-w-md mx-auto">
        <div class="w-16 h-16 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Complete Payment</h2>
        <p class="text-gray-500 mb-6">You are paying <span class="font-bold text-gray-800">{{ currency_format($order->total) }}</span> for your order.</p>

        <form action="{{ route('checkout.verify') }}" method="POST" id="razorpay-form">
            @csrf
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $razorpayOrder['id'] }}">
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            
            <button type="button" id="rzp-button1" class="w-full bg-violet-600 text-white font-bold py-3 rounded-lg hover:bg-violet-700 transition-colors shadow">
                Pay Now
            </button>
            <a href="{{ route('checkout.cancel', $order->id) }}" class="block text-sm text-gray-500 mt-4 hover:text-gray-800 hover:underline">Cancel & Return</a>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $razorpayKey }}",
        "amount": "{{ $razorpayOrder['amount'] }}", 
        "currency": "{{ $razorpayOrder['currency'] }}",
        "name": "{{ config('app.name', 'PetWear') }}",
        "description": "Order #{{ $order->order_number }}",
        "image": "{{ asset('logo.png') }}", // Ensure you have a valid logo or remove this
        "order_id": "{{ $razorpayOrder['id'] }}",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpay-form').submit();
        },
        "prefill": {
            "name": "{{ auth()->user()->name }}",
            "email": "{{ auth()->user()->email }}"
        },
        "theme": {
            "color": "#7c3aed"
        }
    };
    var rzp1 = new Razorpay(options);
    
    // Auto-open checkout widget on page load
    window.onload = function() {
        rzp1.open();
    };

    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
</script>
@endpush
@endsection
