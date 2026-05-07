<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data) {
            $cart->load('items.variant.product');

            $subtotal = $cart->total;
            $discount = 0;
            $couponCode = null;

            // Apply coupon
            $couponData = session('coupon');
            if ($couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                    $coupon->increment('used_count');
                }
            }

            $tax = round(($subtotal - $discount) * 0.00, 2); // Tax rate configurable
            
            // New Shipping Logic
            $shippingService = app(\App\Services\ShippingService::class);
            $shippingType = $shippingService->detectShippingType();
            $shippingCost = $shippingService->calculateShipping($cart, $shippingType);
            
            $total = $subtotal - $discount + $tax + $shippingCost;

            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'shipping_type' => $shippingType,
                'total' => $total,
                'payment_status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cod',
                'shipping_address_id' => $data['shipping_address_id'] ?? null,
                'billing_address_id' => $data['billing_address_id'] ?? null,
                'coupon_code' => $couponCode,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create order items & decrement stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->variant->id,
                    'product_name' => $item->variant->product->name,
                    'variant_info' => $item->variant->variant_label,
                    'sku' => $item->variant->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->variant->effective_price,
                    'total' => $item->variant->effective_price * $item->quantity,
                ]);

                $item->variant->decrementStock($item->quantity);
            }

            // Clear cart & coupon
            $cart->items()->delete();
            session()->forget('coupon');

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status): Order
    {
        if (!$order->canTransitionTo($status)) {
            throw new \Exception("Cannot transition from {$order->status} to {$status}");
        }

        // Restore stock on cancel
        if ($status === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->incrementStock($item->quantity);
                }
            }
            $order->update(['payment_status' => 'refunded']);
        }

        if ($status === 'paid') {
            $order->update(['payment_status' => 'paid']);
        }

        $order->update(['status' => $status]);
        return $order->fresh();
    }

    public function getUserOrders(int $userId, int $perPage = 10)
    {
        return Order::where('user_id', $userId)
            ->with('items.variant.product')
            ->latest()
            ->paginate($perPage);
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)
            ->with(['items.variant.product', 'shippingAddress', 'billingAddress', 'user'])
            ->first();
    }
}
