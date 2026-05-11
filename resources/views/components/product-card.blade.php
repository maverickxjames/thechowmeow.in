<div class="group relative bg-white rounded-xl overflow-hidden border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all duration-300" id="product-{{ $product->id }}">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative aspect-square overflow-hidden bg-gray-50">
            <img src="{{ $product->primary_image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                 loading="lazy">

            {{-- Badges --}}
            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                @if($product->activeVariants->first()?->sale_price)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide leading-none">Sale</span>
                @endif
                @if($product->is_featured)
                    <span class="bg-violet-700 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide leading-none">Featured</span>
                @endif
            </div>

            {{-- Out of Stock Overlay --}}
            @if($product->total_stock <= 0)
                <div class="absolute inset-0 bg-white/55 flex items-center justify-center">
                    <span class="bg-white border border-gray-200 text-gray-500 text-xs font-semibold px-4 py-1.5 rounded-full shadow-sm">Out of Stock</span>
                </div>
            @endif
        </div>

        <div class="p-4">
            <h3 class="text-sm font-semibold text-gray-900 mb-1.5 line-clamp-2 group-hover:text-violet-700 transition-colors leading-snug">{{ $product->name }}</h3>

            {{-- Stars --}}
            <div class="flex items-center gap-1.5 mb-3">
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3 h-3 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-200' }} fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                @if($product->review_count > 0)
                    <span class="text-xs text-gray-400">({{ $product->review_count }})</span>
                @endif
            </div>

            {{-- Price + Stock --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 flex-wrap">
                    @if($product->activeVariants->first()?->sale_price)
                        <span class="text-base font-bold text-gray-900">{{ currency_format($product->activeVariants->first()->sale_price) }}</span>
                        <span class="text-sm text-gray-400 line-through">{{ currency_format($product->activeVariants->first()->price) }}</span>
                    @else
                        <span class="text-base font-bold text-gray-900">{{ currency_format($product->min_price) }}</span>
                        @if($product->min_price != $product->max_price)
                            <span class="text-xs text-gray-400">– {{ currency_format($product->max_price) }}</span>
                        @endif
                    @endif
                </div>
                @if($product->total_stock > 0 && $product->total_stock <= 5)
                    <span class="text-[10px] text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded-full whitespace-nowrap">{{ $product->total_stock }} left</span>
                @endif
            </div>
        </div>
    </a>
</div>
