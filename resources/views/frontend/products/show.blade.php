@extends('layouts.app')
@section('title', $product->meta_title ?: $product->name . ' — ' . config('app.name', 'PetWear'))
@section('meta_description', $product->meta_description ?: $product->short_description)

@push('styles')
<style>
    /* Image Zoom */
    .zoom-container { position: relative; overflow: hidden; cursor: crosshair; }
    .zoom-container img.main-img { transition: transform 0.1s ease-out; transform-origin: center center; }
    .zoom-container:hover img.main-img { transform: scale(2.2); }
    .zoom-container .zoom-lens { display: none; }

    /* Size chart table */
    .size-chart-table th, .size-chart-table td { padding: 0.5rem 0.75rem; text-align: center; font-size: 0.8125rem; }
    .size-chart-table th { background: #f3e8ff; color: #6d28d9; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em; }
    .size-chart-table td { border-bottom: 1px solid #f3f4f6; }
    .size-chart-table tr:last-child td { border-bottom: none; }
    .size-chart-table tr:hover td { background: #faf5ff; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @if($product->categories->first())
            <a href="{{ route('category.show', $product->categories->first()->slug) }}" class="hover:text-violet-700 transition-colors">{{ $product->categories->first()->name }}</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif
        <span class="text-gray-700 font-medium truncate">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-16" x-data="productPage()">

        {{-- Image Gallery --}}
        <div class="space-y-3">
            {{-- Main image with zoom --}}
            <div class="zoom-container aspect-square rounded-xl overflow-hidden bg-white border border-gray-100"
                 @mousemove="zoomMove($event)" @mouseleave="zoomReset($event)">
                <img :src="selectedImage" alt="{{ $product->name }}"
                     class="main-img w-full h-full object-contain"
                     id="main-image"
                     :style="zoomStyle">
            </div>

            {{-- Thumbnails --}}
            @if($product->images->count() > 1)
                <div class="flex gap-2.5 overflow-x-auto pb-1">
                    @foreach($product->images as $image)
                        <button @click="selectedImage = '{{ $image->url }}'"
                                class="w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200 shrink-0 focus:outline-none"
                                :class="selectedImage === '{{ $image->url }}' ? 'border-violet-600 ring-2 ring-violet-100' : 'border-gray-100 hover:border-gray-300'">
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div>
            {{-- Categories --}}
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($product->categories as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}"
                       class="text-xs bg-violet-50 text-violet-700 px-3 py-1 rounded-full hover:bg-violet-100 transition-colors font-medium">{{ $cat->name }}</a>
                @endforeach
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3 leading-snug">{{ $product->name }}</h1>

            {{-- Rating --}}
            <div class="flex items-center gap-2.5 mb-5">
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-200' }} fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-500">{{ number_format($product->average_rating, 1) }} ({{ $product->review_count }} {{ Str::plural('review', $product->review_count) }})</span>
            </div>

            {{-- Price --}}
            <div class="mb-6 pb-6 border-b border-gray-100">
                <template x-if="selectedVariant">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-3xl font-bold text-gray-900" x-text="formatPrice(selectedVariant.effective_price)"></span>
                        <template x-if="selectedVariant.effective_price < selectedVariant.price">
                            <span class="text-lg text-gray-400 line-through" x-text="formatPrice(selectedVariant.price)"></span>
                        </template>
                        <template x-if="selectedVariant.effective_price < selectedVariant.price">
                            <span class="bg-red-50 text-red-600 text-sm font-semibold px-3 py-1 rounded-full border border-red-100"
                                  x-text="Math.round((1 - selectedVariant.effective_price / selectedVariant.price) * 100) + '% off'"></span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Short Description --}}
            @if($product->short_description)
                <p class="text-gray-600 mb-6 leading-relaxed text-sm">{{ $product->short_description }}</p>
            @endif

            {{-- Size Selector --}}
            @php $sizes = $product->activeVariants->pluck('size')->unique()->filter(); @endphp
            @if($sizes->count())
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2.5">
                        <label class="text-sm font-semibold text-gray-800">Size</label>
                        <button type="button" @click="showSizeChart = !showSizeChart"
                                class="text-xs font-semibold text-violet-600 hover:text-violet-700 flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                            Size Guide
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($sizes as $size)
                            <button @click="selectSize('{{ $size }}')"
                                    :class="selectedSize === '{{ $size }}'
                                        ? 'border-violet-600 bg-violet-700 text-white'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-400'"
                                    class="px-4 py-2 border-2 rounded-lg text-sm font-semibold transition-all duration-150 min-w-[3rem] text-center">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Size Chart Modal --}}
            <div x-show="showSizeChart" x-cloak x-transition
                 class="mb-6 bg-violet-50/50 rounded-xl border border-violet-100 overflow-hidden">
                <div class="px-5 py-3 bg-violet-100/60 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-violet-800 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                        Size Chart (All measurements in Inches)
                    </h4>
                    <button @click="showSizeChart = false" class="text-violet-400 hover:text-violet-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Tab switcher --}}
                <div class="flex border-b border-violet-100" x-data="{ chartTab: 'dog' }">
                    <button @click="chartTab = 'dog'" :class="chartTab === 'dog' ? 'bg-white text-violet-700 border-b-2 border-violet-600' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2.5 text-xs font-bold uppercase tracking-wide transition-colors flex items-center justify-center gap-1.5">
                        🐕 Dog
                    </button>
                    <button @click="chartTab = 'cat'" :class="chartTab === 'cat' ? 'bg-white text-violet-700 border-b-2 border-violet-600' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2.5 text-xs font-bold uppercase tracking-wide transition-colors flex items-center justify-center gap-1.5">
                        🐈 Cat
                    </button>
                </div>

                <div class="p-4">
                    {{-- Measurement key --}}
                    <div class="flex flex-wrap gap-x-5 gap-y-1 mb-3 text-xs text-gray-500">
                        <span>① Back Length</span>
                        <span>② Chest Girth</span>
                        <span>③ Bottom Length</span>
                        <span>④ Neck Girth</span>
                    </div>

                    {{-- Dog size chart --}}
                    <div x-show="chartTab === 'dog'">
                        <table class="w-full size-chart-table border border-gray-100 rounded-lg overflow-hidden">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Back Length</th>
                                    <th>Chest Girth</th>
                                    <th>Bottom Length</th>
                                    <th>Neck Girth</th>
                                    <th>Breed Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="font-semibold text-gray-800">XS</td><td>10</td><td>14</td><td>6</td><td>9</td><td class="text-xs text-gray-400">Chihuahua, Pomeranian</td></tr>
                                <tr><td class="font-semibold text-gray-800">S</td><td>12</td><td>17</td><td>8</td><td>11</td><td class="text-xs text-gray-400">Shih Tzu, Maltese</td></tr>
                                <tr><td class="font-semibold text-gray-800">M</td><td>14</td><td>21</td><td>10</td><td>13</td><td class="text-xs text-gray-400">Beagle, Cocker Spaniel</td></tr>
                                <tr><td class="font-semibold text-gray-800">L</td><td>18</td><td>26</td><td>12</td><td>16</td><td class="text-xs text-gray-400">Indie, Dalmatian</td></tr>
                                <tr><td class="font-semibold text-gray-800">XL</td><td>22</td><td>33</td><td>14</td><td>19</td><td class="text-xs text-gray-400">Labrador, Golden Retriever</td></tr>
                                <tr><td class="font-semibold text-gray-800">XXL</td><td>26</td><td>40</td><td>18</td><td>22</td><td class="text-xs text-gray-400">German Shepherd, Husky</td></tr>
                                <tr><td class="font-semibold text-gray-800">3XL</td><td>30</td><td>46</td><td>20</td><td>25</td><td class="text-xs text-gray-400">Rottweiler, Great Dane</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Cat size chart --}}
                    <div x-show="chartTab === 'cat'">
                        <table class="w-full size-chart-table border border-gray-100 rounded-lg overflow-hidden">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Back Length</th>
                                    <th>Chest Girth</th>
                                    <th>Bottom Length</th>
                                    <th>Neck Girth</th>
                                    <th>Weight Range</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="font-semibold text-gray-800">XS-S (Kitten)</td><td>10</td><td>14</td><td>5</td><td>8</td><td class="text-xs text-gray-400">1 – 3 kg</td></tr>
                                <tr><td class="font-semibold text-gray-800">S</td><td>12</td><td>16</td><td>6</td><td>9</td><td class="text-xs text-gray-400">2.5 – 4 kg</td></tr>
                                <tr><td class="font-semibold text-gray-800">M</td><td>14</td><td>19</td><td>7</td><td>11</td><td class="text-xs text-gray-400">4 – 5.5 kg</td></tr>
                                <tr><td class="font-semibold text-gray-800">L</td><td>16</td><td>22</td><td>8</td><td>13</td><td class="text-xs text-gray-400">5 – 7 kg</td></tr>
                                <tr><td class="font-semibold text-gray-800">XL</td><td>18</td><td>26</td><td>10</td><td>15</td><td class="text-xs text-gray-400">6.5 – 9 kg (Maine Coon)</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="text-[11px] text-gray-400 mt-3 leading-relaxed">
                        * Measurements are approximate. Please use a measuring tape and measure your pet before placing an order for the best fit.
                    </p>
                </div>
            </div>

            {{-- Color Selector --}}
            @php $colors = $product->activeVariants->pluck('color')->unique()->filter(); @endphp
            @if($colors->count())
                <div class="mb-5">
                    <label class="text-sm font-semibold text-gray-800 mb-2.5 block">Color</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colors as $color)
                            <button @click="selectColor('{{ $color }}')"
                                    :class="selectedColor === '{{ $color }}'
                                        ? 'border-violet-600 bg-violet-700 text-white'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-400'"
                                    class="px-4 py-2 border-2 rounded-lg text-sm font-semibold transition-all duration-150">
                                {{ $color }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Stock Status --}}
            <template x-if="selectedVariant">
                <div class="mb-6">
                    <template x-if="selectedVariant.stock_quantity > 5">
                        <div class="flex items-center gap-1.5 text-emerald-700 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            In stock
                        </div>
                    </template>
                    <template x-if="selectedVariant.stock_quantity > 0 && selectedVariant.stock_quantity <= 5">
                        <div class="flex items-center gap-1.5 text-amber-700 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Only <span x-text="selectedVariant.stock_quantity" class="font-bold mx-0.5"></span> left
                        </div>
                    </template>
                    <template x-if="selectedVariant.stock_quantity <= 0">
                        <div class="flex items-center gap-1.5 text-red-600 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Out of stock
                        </div>
                    </template>
                </div>
            </template>

            {{-- Add to Cart --}}
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="variant_id" :value="selectedVariant ? selectedVariant.id : ''">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white">
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)"
                                class="w-10 h-11 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors font-bold text-lg">−</button>
                        <input type="number" name="quantity" x-model="quantity" min="1" max="10"
                               class="w-14 h-11 text-center border-0 border-x border-gray-200 focus:ring-0 font-semibold text-sm text-gray-900 bg-white">
                        <button type="button" @click="quantity = Math.min(10, quantity + 1)"
                                class="w-10 h-11 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors font-bold text-lg">+</button>
                    </div>
                    <button type="submit"
                            :disabled="!selectedVariant || selectedVariant.stock_quantity <= 0"
                            class="flex-1 h-11 bg-violet-700 text-white font-semibold rounded-lg hover:bg-violet-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Add to Cart
                    </button>
                </div>
            </form>

            {{-- SKU --}}
            <template x-if="selectedVariant">
                <p class="text-xs text-gray-400">SKU: <span class="font-mono" x-text="selectedVariant.sku"></span></p>
            </template>

            {{-- Trust Signals --}}
            <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-2 gap-3">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Free shipping over {{ currency_format(999) }}
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    7-day easy returns
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Secure checkout
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    24/7 customer support
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($product->description)
        <div class="mt-14 bg-white rounded-xl p-8 border border-gray-100 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Product Description</h2>
            <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">{!! nl2br(e($product->description)) !!}</div>
        </div>
    @endif

    {{-- Reviews --}}
    <div class="mt-8 bg-white rounded-xl p-8 border border-gray-100 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-6">
            Customer Reviews
            @if($product->approvedReviews->count())
                <span class="text-sm font-normal text-gray-400 ml-2">({{ $product->approvedReviews->count() }})</span>
            @endif
        </h2>

        @auth
            <div class="mb-8 bg-gray-50 rounded-xl p-6 border border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Write a Review</h3>
                <form action="{{ route('reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-4" x-data="{ rating: 5 }">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Your Rating</label>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                        class="transition-colors focus:outline-none"
                                        :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-300'">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Your Review</label>
                        <textarea name="comment" rows="3"
                                  class="w-full rounded-lg border-gray-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 text-sm text-gray-700 resize-none"
                                  placeholder="Share your experience with this product..."></textarea>
                    </div>
                    <button type="submit" class="bg-violet-700 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-violet-800 transition-colors">
                        Submit Review
                    </button>
                </form>
            </div>
        @endauth

        @forelse($product->approvedReviews as $review)
            <div class="py-5 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-bold text-sm shrink-0">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $review->user->name }}</p>
                            <div class="flex gap-0.5 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }} fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 shrink-0">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $review->comment }}</p>
            </div>
        @empty
            <div class="text-center py-10">
                <p class="text-sm text-gray-400">No reviews yet. Be the first to share your experience!</p>
            </div>
        @endforelse
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->count())
        <div class="mt-14">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                @foreach($relatedProducts as $prod)
                    @include('components.product-card', ['product' => $prod])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function productPage() {
    const variants = @json($product->activeVariants);
    return {
        variants,
        selectedSize: variants[0]?.size || null,
        selectedColor: variants[0]?.color || null,
        selectedImage: '{{ $product->primary_image_url }}',
        quantity: 1,
        showSizeChart: false,
        zoomStyle: '',

        get selectedVariant() {
            return this.variants.find(v =>
                (!this.selectedSize || v.size === this.selectedSize) &&
                (!this.selectedColor || v.color === this.selectedColor)
            ) || this.variants[0] || null;
        },

        selectSize(size) { this.selectedSize = size; },
        selectColor(color) { this.selectedColor = color; },

        formatPrice(value) {
            const num = parseFloat(value);
            if (isNaN(num)) return '{{ config('petwear.currency_symbol', '₹') }}0';
            
            const enableUsd = {{ config('petwear.enable_usd', false) ? 'true' : 'false' }};
            const activeCurrency = '{{ session('currency', 'INR') }}';
            const exchangeRate = {{ config('petwear.usd_exchange_rate', 83.50) }};

            if (enableUsd && activeCurrency === 'USD') {
                const usdPrice = num / exchangeRate;
                return '$' + usdPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            const symbol = '{{ config('petwear.currency_symbol', '₹') }}';
            return symbol + Math.round(num).toLocaleString('en-IN');
        },

        // Image zoom on hover
        zoomMove(event) {
            const rect = event.currentTarget.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;
            this.zoomStyle = `transform: scale(2.2); transform-origin: ${x}% ${y}%;`;
        },
        zoomReset(event) {
            this.zoomStyle = '';
        },
    }
}
</script>
@endpush
@endsection
