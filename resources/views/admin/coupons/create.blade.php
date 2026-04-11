@extends('layouts.admin')
@section('title', 'Add Coupon')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.coupons.store') }}" method="POST" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-6">
        @csrf
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Code *</label><input type="text" name="code" value="{{ old('code') }}" required class="w-full rounded-lg border-gray-200 uppercase" placeholder="e.g. PETWEAR10"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Type *</label><select name="type" class="w-full rounded-lg border-gray-200"><option value="percent">Percentage</option><option value="fixed">Fixed Amount</option></select></div>
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Value *</label><input type="number" step="0.01" name="value" value="{{ old('value') }}" required class="w-full rounded-lg border-gray-200"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Min Order Amount</label><input type="number" step="0.01" name="min_order" value="{{ old('min_order') }}" class="w-full rounded-lg border-gray-200"></div>
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Max Uses</label><input type="number" name="max_uses" value="{{ old('max_uses') }}" class="w-full rounded-lg border-gray-200" placeholder="Leave empty for unlimited"></div>
        </div>
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Expires At</label><input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full rounded-lg border-gray-200"></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded text-purple-600"><span class="text-sm">Active</span></label>
        <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700">Create Coupon</button>
    </form>
</div>
@endsection
