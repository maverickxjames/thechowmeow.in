@extends('layouts.app')
@section('title', $category->meta_title ?: $category->name . ' — PetWear')

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            @foreach($category->getAncestors() as $ancestor)
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('category.show', $ancestor->slug) }}" class="hover:text-violet-700 transition-colors">{{ $ancestor->name }}</a>
            @endforeach
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">{{ $category->name }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-sm text-gray-500 mt-1 max-w-2xl">{{ $category->description }}</p>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Subcategory Tabs --}}
    @if($subcategories->count())
        <div class="flex flex-wrap gap-2 mb-8">
            @foreach($subcategories as $sub)
                <a href="{{ route('category.show', $sub->slug) }}"
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-violet-50 hover:border-violet-300 hover:text-violet-700 transition-all duration-150 shadow-sm">
                    {{ $sub->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Products --}}
    @if($products->count())
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500">
                <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-10">{{ $products->withQueryString()->links() }}</div>
    @else
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-5">
                <svg class="w-8 h-8 text-gray-400" viewBox="0 0 100 100" fill="currentColor">
                    <ellipse cx="50" cy="68" rx="22" ry="18" opacity="0.3"/>
                    <ellipse cx="28" cy="44" rx="10" ry="12" transform="rotate(-15 28 44)" opacity="0.3"/>
                    <ellipse cx="72" cy="44" rx="10" ry="12" transform="rotate(15 72 44)" opacity="0.3"/>
                    <ellipse cx="38" cy="28" rx="9" ry="11" transform="rotate(-10 38 28)" opacity="0.3"/>
                    <ellipse cx="62" cy="28" rx="9" ry="11" transform="rotate(10 62 28)" opacity="0.3"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No products in this category yet</h3>
            <p class="text-sm text-gray-500 mb-6">Check back soon or explore other categories.</p>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-violet-700 hover:text-violet-900 transition-colors">Browse all products</a>
        </div>
    @endif
</div>
@endsection
