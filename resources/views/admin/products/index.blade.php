@extends('layouts.admin')
@section('title', 'Products')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.products.index') }}" method="GET">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
            </div>

            {{-- Status --}}
            <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'  ? 'selected' : '' }}>Active</option>
                <option value="draft"    {{ request('status') === 'draft'   ? 'selected' : '' }}>Draft</option>
            </select>

            {{-- Featured --}}
            <select name="featured" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Products</option>
                <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
            </select>

            {{-- Category --}}
            <select name="category_id" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 ml-auto">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search','status','featured','category_id']))
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                @endif
                <a href="{{ route('admin.products.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Product
                </a>
            </div>
        </div>
    </form>

    {{-- Active filter chips --}}
    @if(request()->hasAny(['search','status','featured','category_id']))
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
            @if(request('search'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Search: "{{ request('search') }}"
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Status: {{ ucfirst(request('status')) }}
                </span>
            @endif
            @if(request('featured'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Featured only
                </span>
            @endif
            @if(request('category_id'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Category: {{ $categories->find(request('category_id'))?->name }}
                </span>
            @endif
        </div>
    @endif
</div>

{{-- Results count --}}
<div class="flex items-center justify-between mb-3">
    <p class="text-sm text-gray-500">
        Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
        of <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
    </p>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-5 py-3.5">Product</th>
                <th class="px-5 py-3.5">Price</th>
                <th class="px-5 py-3.5">Stock</th>
                <th class="px-5 py-3.5">Categories</th>
                <th class="px-5 py-3.5">Status</th>
                <th class="px-5 py-3.5">Featured</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->primary_image_url }}"
                                 class="w-11 h-11 rounded-lg object-cover bg-gray-100 shrink-0">
                            <div>
                                <p class="font-medium text-gray-900 leading-snug">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $product->activeVariants->count() }} {{ Str::plural('variant', $product->activeVariants->count()) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-gray-900">₹{{ number_format($product->base_price) }}</td>
                    <td class="px-5 py-3.5">
                        @php $stock = $product->activeVariants->sum('stock_quantity'); @endphp
                        <span class="font-semibold {{ $stock <= 0 ? 'text-red-600' : ($stock <= 5 ? 'text-amber-600' : 'text-gray-900') }}">
                            {{ $stock }}
                        </span>
                        @if($stock <= 0)
                            <span class="ml-1 text-xs text-red-500">(out)</span>
                        @elseif($stock <= 5)
                            <span class="ml-1 text-xs text-amber-500">(low)</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex flex-wrap gap-1">
                            @foreach($product->categories->take(2) as $cat)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-md">{{ $cat->name }}</span>
                            @endforeach
                            @if($product->categories->count() > 2)
                                <span class="text-xs text-gray-400">+{{ $product->categories->count() - 2 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $product->is_active ? 'Active' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($product->is_featured)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700">Featured</span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-xs font-semibold text-violet-600 hover:text-violet-800 transition-colors">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <p class="text-sm font-medium text-gray-500">No products found</p>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $products->links() }}</div>

@endsection
