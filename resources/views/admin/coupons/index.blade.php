@extends('layouts.admin')
@section('title', 'Coupons')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.coupons.index') }}" method="GET">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by code…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
            </div>

            {{-- Type --}}
            <select name="type" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Types</option>
                <option value="fixed"   {{ request('type') === 'fixed'   ? 'selected' : '' }}>Fixed</option>
                <option value="percent" {{ request('type') === 'percent' ? 'selected' : '' }}>Percent</option>
            </select>

            {{-- Status --}}
            <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <div class="flex gap-2 ml-auto">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search','type','status']))
                    <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                @endif
                <a href="{{ route('admin.coupons.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('admin.coupons.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Coupon
                </a>
            </div>
        </div>
    </form>

    {{-- Active filter chips --}}
    @if(request()->hasAny(['search','type','status']))
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
            @if(request('search'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Search: "{{ request('search') }}"
                </span>
            @endif
            @if(request('type'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Type: {{ ucfirst(request('type')) }}
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Status: {{ ucfirst(request('status')) }}
                </span>
            @endif
        </div>
    @endif
</div>

{{-- Results count --}}
<div class="flex items-center justify-between mb-3">
    <p class="text-sm text-gray-500">
        Showing <span class="font-semibold text-gray-900">{{ $coupons->firstItem() ?? 0 }}–{{ $coupons->lastItem() ?? 0 }}</span>
        of <span class="font-semibold text-gray-900">{{ $coupons->total() }}</span> coupons
    </p>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-5 py-3.5">Code</th>
                <th class="px-5 py-3.5">Type</th>
                <th class="px-5 py-3.5">Value</th>
                <th class="px-5 py-3.5">Min Order</th>
                <th class="px-5 py-3.5">Uses</th>
                <th class="px-5 py-3.5">Expires</th>
                <th class="px-5 py-3.5">Status</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($coupons as $coupon)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-sm font-semibold text-violet-700 bg-violet-50 px-2 py-0.5 rounded">{{ $coupon->code }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">{{ ucfirst($coupon->type) }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-900">
                        {{ $coupon->type === 'percent' ? $coupon->value . '%' : '₹' . number_format($coupon->value) }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">
                        {{ $coupon->min_order ? '₹' . number_format($coupon->min_order) : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">
                        {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs">
                        {{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'Never' }}
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $coupon->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}"
                               class="text-xs font-semibold text-violet-600 hover:text-violet-800 transition-colors">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                  onsubmit="return confirm('Delete coupon {{ addslashes($coupon->code) }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <p class="text-sm font-medium text-gray-500">No coupons found</p>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or <a href="{{ route('admin.coupons.create') }}" class="text-violet-600 hover:underline">create one</a></p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $coupons->links() }}</div>

@endsection
