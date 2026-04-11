<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $orders = $this->orderService->getUserOrders(auth()->id());
        return view('frontend.orders.index', compact('orders'));
    }

    public function show(string $orderNumber)
    {
        $order = $this->orderService->getOrderByNumber($orderNumber);
        if (!$order || $order->user_id !== auth()->id()) {
            abort(404);
        }
        return view('frontend.orders.show', compact('order'));
    }
}
