@extends('layouts.app')
@section('title', request('search') ? '"' . request('search') . '" — PetWear' : 'All Products — PetWear')

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">All Products</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">
            @if(request('search'))
                Results for "{{ request('search') }}"
            @else
                All Products
            @endif
        </h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row gap-8">

        {{-- Sidebar Filters --}}
        <aside class="w-full md:w-60 shrink-0">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sticky top-24">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-semibold text-gray-900 text-sm">Filters</h3>
                    @if(request()->hasAny(['sort', 'min_price', 'max_price', 'search']))
                        <a href="{{ route('products.index') }}" class="text-xs text-violet-600 hover:text-violet-800 font-medium transition-colors">Clear all</a>
                    @endif
                </div>
                <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    {{-- Sort --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2.5 block">Sort By</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="w-full rounded-lg border-gray-200 text-sm text-gray-700 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 bg-white py-2 pr-8">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A–Z</option>
                        </select>
                    </div>

                    {{-- Price Range --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2.5 block">Price Range</label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">{{ config('petwear.currency_symbol', '₹') }}</span>
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                       placeholder="Min"
                                       class="w-full rounded-lg border-gray-200 text-sm pl-6 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100">
                            </div>
                            <span class="text-gray-300 text-sm">–</span>
                            <div class="relative flex-1">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">{{ config('petwear.currency_symbol', '₹') }}</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                       placeholder="Max"
                                       class="w-full rounded-lg border-gray-200 text-sm pl-6 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-violet-700 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-violet-800 transition-colors">
                        Apply Filters
                    </button>
                </form>
            </div>
        </aside>

        {{-- Products Grid --}}
        <div class="flex-1 min-w-0">
            {{-- Grid Header --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    @if($products->total() > 0)
                        Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span> of <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
                    @endif
                </p>
                {{-- Active filter chips --}}
                <div class="flex flex-wrap gap-2">
                    @if(request('min_price') || request('max_price'))
                        <span class="inline-flex items-center gap-1.5 text-xs bg-violet-50 text-violet-700 font-medium px-3 py-1 rounded-full border border-violet-100">
                            {{ currency_format(request('min_price', 0)) }} – {{ request('max_price') ? currency_format(request('max_price')) : '∞' }}
                        </span>
                    @endif
                </div>
            </div>

            @if($products->count())
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-10">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No products found</h3>
                    <p class="text-sm text-gray-500 mb-6 max-w-xs">Try adjusting your filters or search terms to find what you're looking for.</p>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-violet-700 hover:text-violet-900 transition-colors">Clear all filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
