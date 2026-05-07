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

            {{-- Rows Per Page --}}
            <select name="per_page" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white" onchange="this.form.submit()">
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 rows</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 rows</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
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
                <a href="{{ route('admin.import.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
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
<div x-data="{ selectedIds: [], selectAll: false }"
     x-init="$watch('selectAll', value => {
         selectedIds = value ? Array.from(document.querySelectorAll('.product-checkbox')).map(cb => cb.value) : []
     })">

    <form action="{{ route('admin.products.bulk-destroy') }}" method="POST" id="bulk-delete-form">
        @csrf
        @method('DELETE')
        
        <div class="flex items-center justify-between mb-3 min-h-[32px]">
            <div x-show="selectedIds.length > 0" x-transition.opacity style="display: none;">
                <button type="button" 
                        @click="if(confirm(`Delete ${selectedIds.length} selected product(s)? This cannot be undone.`)) document.getElementById('bulk-delete-form').submit()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium rounded-lg transition-colors border border-red-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Selected (<span x-text="selectedIds.length"></span>)
                </button>
            </div>
            <div x-show="selectedIds.length === 0" class="text-sm text-gray-500"></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-5 py-3.5 w-12 text-center">
                            <input type="checkbox" x-model="selectAll" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        </th>
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
                        <tr class="hover:bg-gray-50 transition-colors" :class="selectedIds.includes('{{ $product->id }}') ? 'bg-violet-50/50' : ''">
                            <td class="px-5 py-3.5 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" x-model="selectedIds" class="product-checkbox rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                            </td>
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
                                    <button type="button" 
                                            @click="if(confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')) { 
                                                const form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = '{{ route('admin.products.destroy', $product) }}';
                                                form.innerHTML = '<input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'><input type=\'hidden\' name=\'_method\' value=\'DELETE\'>';
                                                document.body.appendChild(form);
                                                form.submit();
                                            }"
                                            class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <p class="text-sm font-medium text-gray-500">No products found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

<div class="mt-5">{{ $products->links() }}</div>

@endsection
