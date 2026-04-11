@extends('layouts.app')
@section('title', 'PetWear — Premium Pet Clothing for Dogs & Cats')
@section('meta_description', 'Shop the best clothing for your beloved dogs and cats. Casual wear, festive outfits, accessories and more at PetWear.')

@section('content')

{{-- Hero Slider --}}
<section class="relative overflow-hidden bg-gray-900"
         x-data="{ current: 0, total: {{ $banners->count() ?: 1 }} }"
         x-init="setInterval(() => current = (current + 1) % total, 5500)">
    <div class="relative h-[480px] md:h-[560px] lg:h-[620px]">
        @forelse($banners as $index => $banner)
            <div x-show="current === {{ $index }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-950/80 via-gray-950/40 to-transparent z-10"></div>
                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 z-20 flex items-center">
                    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
                        <div class="max-w-xl">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4 tracking-tight">{{ $banner->title }}</h1>
                            @if($banner->subtitle)
                                <p class="text-base md:text-lg text-gray-300 mb-8 leading-relaxed">{{ $banner->subtitle }}</p>
                            @endif
                            @if($banner->button_text)
                                <a href="{{ $banner->button_url ?? route('products.index') }}"
                                   class="inline-flex items-center gap-2 px-7 py-3.5 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm shadow-lg">
                                    {{ $banner->button_text }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-violet-950 to-gray-900 flex items-center">
                <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
                    <div class="max-w-xl">
                        <p class="text-violet-400 text-xs font-semibold uppercase tracking-widest mb-4">New Collection</p>
                        <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight mb-5 tracking-tight">Dress Your Pet<br>in Style</h1>
                        <p class="text-gray-400 text-lg mb-8 leading-relaxed">Premium clothing for dogs and cats — from casual to festive.</p>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 px-7 py-3.5 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors shadow-lg">
                            Shop the Collection
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Slider Dots --}}
    @if($banners->count() > 1)
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2">
            @foreach($banners as $index => $banner)
                <button @click="current = {{ $index }}"
                        :class="current === {{ $index }} ? 'bg-white w-6' : 'bg-white/40 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
            @endforeach
        </div>
    @endif
</section>

{{-- Trust / USP Strip --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
            <div class="flex items-center gap-3 px-4 md:px-8 py-5">
                <svg class="w-5 h-5 text-violet-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Free Shipping</p>
                    <p class="text-xs text-gray-400 mt-0.5">On orders over {{ currency_format(999) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 md:px-8 py-5">
                <svg class="w-5 h-5 text-violet-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Secure Payments</p>
                    <p class="text-xs text-gray-400 mt-0.5">100% protected</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 md:px-8 py-5">
                <svg class="w-5 h-5 text-violet-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Easy Returns</p>
                    <p class="text-xs text-gray-400 mt-0.5">7-day return policy</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 md:px-8 py-5">
                <svg class="w-5 h-5 text-violet-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                <div>
                    <p class="text-xs font-semibold text-gray-900">24/7 Support</p>
                    <p class="text-xs text-gray-400 mt-0.5">Always here to help</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Shop by Category --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex items-end justify-between mb-10">
        <div>
            <p class="text-violet-700 text-xs font-semibold uppercase tracking-widest mb-2">Browse</p>
            <h2 class="text-3xl font-bold text-gray-900">Shop by Category</h2>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm font-medium text-violet-700 hover:text-violet-900 flex items-center gap-1 transition-colors">
            All products
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}"
               class="group relative rounded-xl overflow-hidden bg-gray-100 aspect-square">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-violet-100 to-violet-200 flex items-center justify-center">
                        <svg class="w-12 h-12 text-violet-400 opacity-60" viewBox="0 0 100 100" fill="currentColor">
                            <ellipse cx="50" cy="68" rx="22" ry="18"/>
                            <ellipse cx="28" cy="44" rx="10" ry="12" transform="rotate(-15 28 44)"/>
                            <ellipse cx="72" cy="44" rx="10" ry="12" transform="rotate(15 72 44)"/>
                            <ellipse cx="38" cy="28" rx="9" ry="11" transform="rotate(-10 38 28)"/>
                            <ellipse cx="62" cy="28" rx="9" ry="11" transform="rotate(10 62 28)"/>
                        </svg>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/65 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-3.5">
                    <p class="text-white text-sm font-semibold leading-snug">{{ $category->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- Featured Products --}}
@if($featuredProducts->count())
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-violet-700 text-xs font-semibold uppercase tracking-widest mb-2">Handpicked</p>
                <h2 class="text-3xl font-bold text-gray-900">Featured Products</h2>
            </div>
            <a href="{{ route('products.index', ['featured' => 1]) }}" class="text-sm font-medium text-violet-700 hover:text-violet-900 flex items-center gap-1 transition-colors">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Brand / Story Banner --}}
<section class="bg-violet-900 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-violet-300 text-xs font-semibold uppercase tracking-widest mb-4">Our Promise</p>
                <h2 class="text-4xl font-bold text-white leading-snug mb-5">Every Pet Deserves<br>to Look Their Best</h2>
                <p class="text-violet-200/80 leading-relaxed mb-8 text-base">From cozy winter wear to dazzling party outfits, we craft premium clothing that keeps your pets comfortable, happy, and stylish — because they deserve nothing less.</p>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm">
                    Explore the Collection
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-violet-800/50 rounded-xl p-5 border border-violet-700/30">
                    <div class="w-10 h-10 bg-violet-700 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <p class="font-semibold text-white text-sm mb-1.5">Premium Fabrics</p>
                    <p class="text-violet-300 text-xs leading-relaxed">Soft, breathable materials your pet will love</p>
                </div>
                <div class="bg-violet-800/50 rounded-xl p-5 border border-violet-700/30">
                    <div class="w-10 h-10 bg-violet-700 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </div>
                    <p class="font-semibold text-white text-sm mb-1.5">XS to 3XL Sizes</p>
                    <p class="text-violet-300 text-xs leading-relaxed">Perfect fit for every breed and size</p>
                </div>
                <div class="bg-violet-800/50 rounded-xl p-5 border border-violet-700/30">
                    <div class="w-10 h-10 bg-violet-700 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <p class="font-semibold text-white text-sm mb-1.5">Fast Delivery</p>
                    <p class="text-violet-300 text-xs leading-relaxed">Delivered to your door in 3–5 days</p>
                </div>
                <div class="bg-violet-800/50 rounded-xl p-5 border border-violet-700/30">
                    <div class="w-10 h-10 bg-violet-700 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <p class="font-semibold text-white text-sm mb-1.5">Made with Care</p>
                    <p class="text-violet-300 text-xs leading-relaxed">Crafted by pet lovers, for pet lovers</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Best Sellers --}}
@if($bestSellers->count())
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-violet-700 text-xs font-semibold uppercase tracking-widest mb-2">Most Loved</p>
                <h2 class="text-3xl font-bold text-gray-900">Best Sellers</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-sm font-medium text-violet-700 hover:text-violet-900 flex items-center gap-1 transition-colors">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($bestSellers as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Customer Reviews --}}
@if($latestReviews->count())
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-violet-700 text-xs font-semibold uppercase tracking-widest mb-2">Testimonials</p>
            <h2 class="text-3xl font-bold text-gray-900">What Our Customers Say</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latestReviews as $review)
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
                    <div class="flex gap-0.5 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }} fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed flex-1 mb-5">"{{ Str::limit($review->comment, 150) }}"</p>
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                        <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-semibold text-sm shrink-0">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $review->user->name ?? 'Anonymous' }}</p>
                            @if($review->product)
                                <p class="text-xs text-gray-400">on {{ $review->product->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
