<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->search, fn($q, $v) => $q->where('order_number', 'like', "%{$v}%")
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$v}%")))
            ->when($request->status,         fn($q, $v) => $q->where('status', $v))
            ->when($request->payment_status, fn($q, $v) => $q->where('payment_status', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to,   fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function export(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->search, fn($q, $v) => $q->where('order_number', 'like', "%{$v}%")
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$v}%")))
            ->when($request->status,         fn($q, $v) => $q->where('status', $v))
            ->when($request->payment_status, fn($q, $v) => $q->where('payment_status', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to,   fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->get();

        $filename = 'orders_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Customer', 'Email', 'Subtotal', 'Discount', 'Shipping', 'Total', 'Status', 'Payment Status', 'Date']);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->order_number,
                    $o->user->name ?? 'N/A',
                    $o->user->email ?? 'N/A',
                    $o->subtotal,
                    $o->discount ?? 0,
                    $o->shipping_cost ?? 0,
                    $o->total,
                    ucfirst($o->status),
                    ucfirst($o->payment_status),
                    $o->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function show(Order $order)
    {
        $order->load(['items.variant.product', 'user', 'shippingAddress', 'billingAddress']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,paid,shipped,delivered,cancelled']);

        try {
            $this->orderService->updateStatus($order, $request->status);
            return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
