@extends('layouts.admin')
@section('title', 'Inventory')
@section('subtitle', 'Track and manage product stock levels')

@push('styles')
<style>
    @media print {
        aside, header, .flex.gap-2.mb-5, form, .flex.items-center.gap-2 { display: none !important; }
        .bg-white { box-shadow: none !important; border: none !important; }
        td:last-child, th:last-child { display: none !important; }
        body { padding: 0 !important; background: white !important; }
        .max-w-7xl, main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
    }
</style>
@endpush

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['total_variants'] }}</p>
                <p class="text-xs text-gray-500">Total Variants</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ $summary['in_stock'] }}</p>
                <p class="text-xs text-gray-500">In Stock</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-amber-500">{{ $summary['low_stock'] }}</p>
                <p class="text-xs text-gray-500">Low Stock</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-500">{{ $summary['out_of_stock'] }}</p>
                <p class="text-xs text-gray-500">Out of Stock</p>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-2 mb-5">
    <a href="{{ route('admin.inventory.index', ['tab' => 'all']) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
           {{ $tab === 'all' ? 'bg-violet-100 text-violet-800' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        All Inventory
        <span class="text-xs font-bold bg-white/50 px-1.5 py-0.5 rounded" style="background-color: rgba(255,255,255,0.4);">{{ $summary['total_variants'] }}</span>
    </a>
    <a href="{{ route('admin.inventory.index', ['tab' => 'low_stock']) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
           {{ $tab === 'low_stock' ? 'bg-amber-100 text-amber-800' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
        Low Stock
        <span class="text-xs font-bold bg-white/50 px-1.5 py-0.5 rounded">{{ $summary['low_stock'] }}</span>
    </a>
    <a href="{{ route('admin.inventory.index', ['tab' => 'out_of_stock']) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
           {{ $tab === 'out_of_stock' ? 'bg-red-100 text-red-800' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        Out of Stock
        <span class="text-xs font-bold bg-white/50 px-1.5 py-0.5 rounded">{{ $summary['out_of_stock'] }}</span>
    </a>
</div>

{{-- Actions & Filters --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
    {{-- Search Form --}}
    <form action="{{ route('admin.inventory.index') }}" method="GET" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU or name..." class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-violet-200 focus:border-violet-400 w-full md:w-64">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">Filter</button>
        @if(request('search'))
            <a href="{{ route('admin.inventory.index', ['tab' => $tab]) }}" class="px-4 py-2 text-red-600 text-sm font-semibold hover:bg-red-50 rounded-lg transition-colors">Clear</a>
        @endif
    </form>

    {{-- Export / Print Actions --}}
    <div class="flex items-center gap-2" x-data="{ exportOpen: false }">
        <div class="relative">
            <button @click="exportOpen = !exportOpen" @click.away="exportOpen = false" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Export
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="exportOpen" x-transition.opacity x-cloak class="absolute right-0 mt-1 w-40 bg-white border border-gray-100 rounded-lg shadow-lg py-1 z-10">
                <a href="{{ route('admin.inventory.export', ['tab' => $tab, 'search' => request('search'), 'format' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM7 11h2v2H7v-2zm0 4h2v2H7v-2zm4-4h2v2h-2v-2zm0 4h2v2h-2v-2zm4-4h2v2h-2v-2z"/></svg> Excel (.csv)
                </a>
                <a href="{{ route('admin.inventory.export', ['tab' => $tab, 'search' => request('search'), 'format' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> CSV
                </a>
            </div>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print / PDF
        </button>
    </div>
</div>

{{-- Results count --}}
<div class="flex items-center justify-between mb-3 text-sm text-gray-500">
    <p>
        Showing <span class="font-semibold text-gray-900">{{ $variants->firstItem() ?? 0 }}–{{ $variants->lastItem() ?? 0 }}</span>
        of <span class="font-semibold text-gray-900">{{ $variants->total() }}</span> variants
    </p>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-5 py-3.5">Product</th>
                <th class="px-5 py-3.5">Variant</th>
                <th class="px-5 py-3.5">SKU</th>
                <th class="px-5 py-3.5">Stock</th>
                <th class="px-5 py-3.5">Update Stock</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($variants as $variant)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $variant->product->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $variant->variant_label }}</td>
                    <td class="px-5 py-3.5"><span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $variant->sku }}</span></td>
                    <td class="px-5 py-3.5">
                        @if($variant->stock_quantity <= 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {{ $variant->stock_quantity }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {{ $variant->stock_quantity }}
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <form action="{{ route('admin.inventory.update') }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                            <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" min="0"
                                   class="w-20 text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-600 text-white text-xs font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Save
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-sm font-medium text-gray-500">
                                No {{ $tab === 'out_of_stock' ? 'out-of-stock' : 'low-stock' }} variants found
                            </p>
                            <p class="text-xs text-gray-400 mt-1">All inventory levels are healthy</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $variants->withQueryString()->links() }}</div>

@endsection
