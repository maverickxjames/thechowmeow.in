<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $coupon = session('coupon');
        return view('frontend.cart.index', compact('cart', 'coupon'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        $this->cartService->addItem($request->variant_id, $request->quantity ?? 1);

        if ($request->ajax()) {
            $cart = $this->cartService->getCart();
            return response()->json([
                'success' => true,
                'message' => 'Added to cart!',
                'cart_count' => $cart->item_count,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $this->cartService->updateQuantity($request->cart_item_id, $request->quantity);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(Request $request)
    {
        $this->cartService->removeItem($request->cart_item_id);
        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $result = $this->cartService->applyCoupon($request->code);

        return redirect()->route('cart.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();
        return redirect()->route('cart.index')->with('success', 'Coupon removed.');
    }
}
