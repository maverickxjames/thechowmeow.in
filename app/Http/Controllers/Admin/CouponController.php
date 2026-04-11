<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::when($request->search, fn($q, $v) => $q->where('code', 'like', "%{$v}%"))
            ->when($request->type,   fn($q, $v) => $q->where('type', $v))
            ->when($request->status === 'active',   fn($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function export(Request $request)
    {
        $coupons = Coupon::when($request->search, fn($q, $v) => $q->where('code', 'like', "%{$v}%"))
            ->when($request->type,   fn($q, $v) => $q->where('type', $v))
            ->when($request->status === 'active',   fn($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->get();

        $filename = 'coupons_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($coupons) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Code', 'Type', 'Value', 'Min Order', 'Used', 'Max Uses', 'Expires At', 'Status']);
            foreach ($coupons as $c) {
                fputcsv($out, [
                    $c->code,
                    ucfirst($c->type),
                    $c->type === 'percent' ? $c->value . '%' : '₹' . $c->value,
                    $c->min_order ?? '',
                    $c->used_count,
                    $c->max_uses ?? 'Unlimited',
                    $c->expires_at ? $c->expires_at->format('Y-m-d') : 'Never',
                    $c->is_active ? 'Active' : 'Inactive',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }
}
