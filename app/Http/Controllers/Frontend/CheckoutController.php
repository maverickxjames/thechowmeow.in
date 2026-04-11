<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = auth()->user()->addresses;
        $coupon = session('coupon');

        return view('frontend.checkout.index', compact('cart', 'addresses', 'coupon'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required_without:new_address.name|nullable|exists:addresses,id',
            'new_address.name' => 'required_without:shipping_address_id|nullable|string|max:255',
            'new_address.phone' => 'nullable|string|max:20',
            'new_address.address_line_1' => 'required_without:shipping_address_id|nullable|string|max:255',
            'new_address.city' => 'required_without:shipping_address_id|nullable|string|max:255',
            'new_address.state' => 'required_without:shipping_address_id|nullable|string|max:255',
            'new_address.zip' => 'required_without:shipping_address_id|nullable|string|max:10',
            'payment_method' => 'required|in:cod,razorpay',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->cartService->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Create new address if needed
        $addressId = $request->shipping_address_id;
        if (!$addressId && $request->has('new_address')) {
            $address = Address::create(array_merge($request->new_address, [
                'user_id' => auth()->id(),
                'type' => 'shipping',
            ]));
            $addressId = $address->id;
        }

        $order = $this->orderService->createOrder($cart, [
            'shipping_address_id' => $addressId,
            'billing_address_id' => $addressId,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        if ($request->payment_method === 'razorpay') {
            return redirect()->route('checkout.payment', $order->id);
        }

        return redirect()->route('orders.show', $order->order_number)
            ->with('success', 'Order placed successfully! Order #' . $order->order_number);
    }

    private function getRazorpayApi()
    {
        $mode = \App\Models\Setting::where('key', 'razorpay_mode')->value('value') ?? 'test';
        $key = \App\Models\Setting::where('key', "razorpay_{$mode}_key")->value('value');
        $secret = \App\Models\Setting::where('key', "razorpay_{$mode}_secret")->value('value');

        return new \Razorpay\Api\Api($key, $secret);
    }

    public function payment(\App\Models\Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->payment_status === 'paid' || $order->payment_method !== 'razorpay') {
            return redirect()->route('orders.show', $order->order_number);
        }

        if ((float) $order->total < 1) {
            return redirect()->route('orders.show', $order->order_number)
                ->with('error', 'Order amount is too low to process via Razorpay.');
        }

        $api = $this->getRazorpayApi();
        $currency = get_active_currency();
        $convertedAmount = get_converted_amount($order->total);
        
        try {
            $razorpayOrder = $api->order->create([
                'receipt'         => (string) $order->id,
                'amount'          => (int) round((float) $convertedAmount * 100), // amount in subunits (paisa/cents)
                'currency'        => strtoupper($currency),
                'payment_capture' => 1
            ]);
        } catch (\Exception $e) {
            return redirect()->route('orders.show', $order->order_number)
                ->with('error', 'Razorpay Error: ' . $e->getMessage());
        }

        $order->update(['transaction_id' => $razorpayOrder['id']]);

        $mode = \App\Models\Setting::where('key', 'razorpay_mode')->value('value') ?? 'test';
        $razorpayKey = \App\Models\Setting::where('key', "razorpay_{$mode}_key")->value('value');

        return view('frontend.checkout.payment', compact('order', 'razorpayOrder', 'razorpayKey'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'order_id' => 'required|exists:orders,id'
        ]);

        $api = $this->getRazorpayApi();

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];
            $api->utility->verifyPaymentSignature($attributes);
            
            $order = \App\Models\Order::findOrFail($request->order_id);
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);

            return redirect()->route('orders.show', $order->order_number)->with('success', 'Payment successful!');
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            $order = \App\Models\Order::findOrFail($request->order_id);
            
            // Restore cart
            $cartService = app(\App\Services\CartService::class);
            foreach($order->items as $item) {
                $cartService->addToCart($item->product_variant_id, $item->quantity);
            }
            app(\App\Services\OrderService::class)->updateStatus($order, 'cancelled');

            return redirect()->route('checkout.index')->with('error', 'Payment verification failed. Please try again.');
        }
    }

    public function cancel(\App\Models\Order $order)
    {
        if ($order->user_id === auth()->id() && $order->payment_status === 'pending' && $order->payment_method === 'razorpay') {
            app(\App\Services\OrderService::class)->updateStatus($order, 'cancelled');
            
            // Restore cart
            $cartService = app(\App\Services\CartService::class);
            foreach($order->items as $item) {
                $cartService->addToCart($item->product_variant_id, $item->quantity);
            }
            return redirect()->route('checkout.index')->with('error', 'Payment was cancelled. You can try again.');
        }
        return redirect()->route('orders.index');
    }
}
