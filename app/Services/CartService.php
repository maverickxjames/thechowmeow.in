<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function getCart(): ?Cart
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.variant.product.primaryImage');
        return $cart;
    }

    public function addItem(int $variantId, int $quantity = 1): CartItem
    {
        $cart = $this->getOrCreateCart();
        $variant = ProductVariant::findOrFail($variantId);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $newQty = min($cartItem->quantity + $quantity, $variant->stock_quantity);
            $cartItem->update(['quantity' => $newQty]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variantId,
                'quantity' => min($quantity, $variant->stock_quantity),
            ]);
        }

        return $cartItem;
    }

    public function updateQuantity(int $cartItemId, int $quantity): ?CartItem
    {
        $cart = $this->getOrCreateCart();
        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();
            return null;
        }

        $maxQty = $cartItem->variant->stock_quantity;
        $cartItem->update(['quantity' => min($quantity, $maxQty)]);
        return $cartItem;
    }

    public function removeItem(int $cartItemId): bool
    {
        $cart = $this->getOrCreateCart();
        return CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->delete();
    }

    public function clear(): bool
    {
        $cart = $this->getOrCreateCart();
        return $cart->items()->delete();
    }

    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();
        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        $cart = $this->getCart();
        $subtotal = $cart->total;

        if (!$coupon->isValid($subtotal)) {
            return ['success' => false, 'message' => 'Coupon is not valid for this order.'];
        }

        $discount = $coupon->calculateDiscount($subtotal);
        session(['coupon' => [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ]]);

        return ['success' => true, 'discount' => $discount, 'message' => 'Coupon applied!'];
    }

    public function removeCoupon(): void
    {
        session()->forget('coupon');
    }

    public function mergeGuestCart(): void
    {
        if (!Auth::check()) return;

        $sessionId = session()->getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();
        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_variant_id', $item->product_variant_id)->first();
            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $item->quantity]);
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }

        $guestCart->delete();
    }
}
