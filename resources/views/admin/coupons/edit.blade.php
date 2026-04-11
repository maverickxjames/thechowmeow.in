@extends('layouts.admin')
@section('title', 'Edit Coupon: ' . $coupon->code)

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-6">
        @csrf @method('PUT')
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Code *</label><input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="w-full rounded-lg border-gray-200 uppercase"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Type *</label><select name="type" class="w-full rounded-lg border-gray-200"><option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percentage</option><option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option></select></div>
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Value *</label><input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value) }}" required class="w-full rounded-lg border-gray-200"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Min Order Amount</label><input type="number" step="0.01" name="min_order" value="{{ old('min_order', $coupon->min_order) }}" class="w-full rounded-lg border-gray-200"></div>
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Max Uses</label><input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" class="w-full rounded-lg border-gray-200"></div>
        </div>
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Expires At</label><input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border-gray-200"></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }} class="rounded text-purple-600"><span class="text-sm">Active</span></label>
        <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700">Update Coupon</button>
    </form>
</div>
@endsection
