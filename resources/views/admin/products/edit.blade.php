@extends('layouts.admin')
@section('title', 'Edit: ' . $product->name)
@section('subtitle', 'Update product details, images, and variants')

@section('content')
<div class="max-w-5xl space-y-6">

    {{-- Product Details --}}
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="productForm">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-bold text-gray-900">Product Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Short Description</label>
                    <textarea name="short_description" rows="2" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ old('short_description', $product->short_description) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Description</label>
                    <textarea name="description" rows="5" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ old('description', $product->description) }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Base Price <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₹</span>
                        <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" required
                               class="w-full pl-7 rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                    </div>
                </div>
                <div class="flex items-center gap-6 pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="rounded text-violet-600 focus:ring-violet-200">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="rounded text-violet-600 focus:ring-violet-200">
                        <span class="text-sm text-gray-700">Featured</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h3 class="font-bold text-gray-900">Categories</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $cat)
                    <label class="flex items-center gap-2 bg-gray-50 hover:bg-violet-50 px-3.5 py-2 rounded-lg cursor-pointer transition-colors border border-transparent has-[:checked]:border-violet-300 has-[:checked]:bg-violet-50">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ $product->categories->contains($cat->id) ? 'checked' : '' }}
                               class="rounded text-violet-600 focus:ring-violet-200">
                        <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Images Section --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5" x-data="imageManager()">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">Product Images</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Drag images to reorder. First image is the primary.</p>
                </div>
            </div>

            {{-- Existing Images --}}
            @if($product->images->count())
                <div>
                    <p class="text-xs font-medium text-gray-500 mb-2">Current Images</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach($product->images->sortBy('sort_order') as $index => $image)
                            <div class="relative group aspect-square rounded-xl overflow-hidden border-2 transition-all {{ $image->is_primary ? 'border-violet-400 ring-2 ring-violet-100' : 'border-gray-200' }}"
                                 id="existing-image-{{ $image->id }}">
                                <img src="{{ $image->url }}" class="w-full h-full object-cover" alt="">
                                @if($image->is_primary)
                                    <div class="absolute top-1.5 left-1.5">
                                        <span class="px-1.5 py-0.5 bg-violet-600 text-white text-[10px] font-bold rounded">PRIMARY</span>
                                    </div>
                                @endif
                                <button type="button"
                                        onclick="deleteImage({{ $product->id }}, {{ $image->id }}, this)"
                                        class="absolute bottom-1.5 right-1.5 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Drop Zone for new images --}}
            <div>
                <p class="text-xs font-medium text-gray-500 mb-2">Add New Images</p>
                <div @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="handleDrop($event)"
                     @click="$refs.fileInput.click()"
                     :class="dragging ? 'border-violet-400 bg-violet-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
                     class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <p class="text-sm text-gray-600 font-medium">Drag & drop images or click to browse</p>
                        <p class="text-xs text-gray-400">PNG, JPG, WEBP • Max 2MB each</p>
                    </div>
                </div>
                <input type="file" x-ref="fileInput" @change="handleFiles($event)" multiple accept="image/*" class="hidden">
            </div>

            {{-- New Image Previews (sortable) --}}
            <div x-show="files.length > 0">
                <p class="text-xs font-medium text-gray-500 mb-2">New Images <span class="text-gray-400" x-text="'(' + files.length + ')'"></span></p>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <template x-for="(file, index) in files" :key="file.id">
                        <div class="relative group aspect-square rounded-xl overflow-hidden border-2 border-gray-200 transition-all cursor-grab active:cursor-grabbing"
                             draggable="true"
                             @dragstart="dragStart(index, $event)"
                             @dragover.prevent="dragOver(index, $event)"
                             @dragend="dragEnd()"
                             :data-index="index">
                            <img :src="file.preview" class="w-full h-full object-cover">
                            <div class="absolute top-1.5 right-1.5">
                                <span class="w-5 h-5 flex items-center justify-center bg-black/50 text-white text-[10px] font-bold rounded-full" x-text="index + 1"></span>
                            </div>
                            <button @click.stop="removeFile(index)" type="button"
                                    class="absolute bottom-1.5 right-1.5 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow">
                                ×
                            </button>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-70 drop-shadow transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Hidden file inputs --}}
            <div x-ref="hiddenInputs"></div>
        </div>

        {{-- SEO --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h3 class="font-bold text-gray-900">SEO</h3>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                       class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Meta Description</label>
                <textarea name="meta_description" rows="2" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ old('meta_description', $product->meta_description) }}</textarea>
            </div>
        </div>

        <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">
            Update Product
        </button>
    </form>

    {{-- Variants Section --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-900">Product Variants</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $product->variants->count() }} variant(s)</p>
            </div>
        </div>

        {{-- Existing Variants --}}
        @if($product->variants->count())
            <div class="overflow-x-auto mb-6 rounded-lg border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3">Size</th>
                            <th class="px-4 py-3">Color</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Sale</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($product->variants as $variant)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $variant->size ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $variant->color ?? '—' }}</td>
                                <td class="px-4 py-3"><span class="font-mono text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ $variant->sku }}</span></td>
                                <td class="px-4 py-3 font-medium">₹{{ number_format($variant->price) }}</td>
                                <td class="px-4 py-3 text-emerald-600 font-medium">{{ $variant->sale_price ? '₹'.number_format($variant->sale_price) : ($variant->discount_percent ? $variant->discount_percent.'%' : '—') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold {{ $variant->stock_quantity <= 5 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                                        {{ $variant->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.variants.destroy', $variant) }}" method="POST" onsubmit="return confirm('Delete this variant?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 mb-6 border border-dashed border-gray-200 rounded-xl">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm text-gray-500">No variants yet. Add one below.</p>
            </div>
        @endif

        {{-- Add Variant Form --}}
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST"
              class="bg-gray-50 rounded-xl p-5 border border-gray-100">
            @csrf
            <h4 class="font-semibold text-gray-800 text-sm mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New Variant
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Size</label>
                    <select name="size" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                        <option value="">Select</option>
                        @foreach(['XS','S','M','L','XL','XXL','3XL'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Color</label>
                    <input type="text" name="color" placeholder="e.g. Red" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">SKU <span class="text-red-400">*</span></label>
                    <input type="text" name="sku" required class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Price <span class="text-red-400">*</span></label>
                    <input type="number" step="0.01" name="price" required class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Discount %</label>
                    <input type="number" name="discount_percent" min="0" max="100" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Stock <span class="text-red-400">*</span></label>
                    <input type="number" name="stock_quantity" required value="0" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold hover:bg-violet-700 transition-colors inline-flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Variant
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function imageManager() {
    return {
        files: [],
        dragging: false,
        dragIndex: null,
        nextId: 0,

        handleFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.addFiles(newFiles);
            event.target.value = '';
        },

        handleDrop(event) {
            this.dragging = false;
            const newFiles = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            this.addFiles(newFiles);
        },

        addFiles(newFiles) {
            newFiles.forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    alert(file.name + ' exceeds 2MB limit');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.files.push({
                        id: this.nextId++,
                        file: file,
                        preview: e.target.result,
                        name: file.name
                    });
                    this.updateHiddenInputs();
                };
                reader.readAsDataURL(file);
            });
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.updateHiddenInputs();
        },

        dragStart(index, event) {
            this.dragIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.target.style.opacity = '0.5';
        },

        dragOver(index, event) {
            if (this.dragIndex === null || this.dragIndex === index) return;
            const item = this.files.splice(this.dragIndex, 1)[0];
            this.files.splice(index, 0, item);
            this.dragIndex = index;
            this.updateHiddenInputs();
        },

        dragEnd() {
            this.dragIndex = null;
            document.querySelectorAll('[data-index]').forEach(el => el.style.opacity = '1');
        },

        updateHiddenInputs() {
            const container = this.$refs.hiddenInputs;
            container.innerHTML = '';
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.file));
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'images[]';
            input.multiple = true;
            input.files = dt.files;
            input.style.display = 'none';
            container.appendChild(input);
        }
    };
}

function deleteImage(productId, imageId, btn) {
    if (!confirm('Remove this image?')) return;
    fetch('/admin/products/' + productId + '/images', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ image_id: imageId })
    }).then(res => {
        if (res.ok) {
            const card = document.getElementById('existing-image-' + imageId);
            if (card) card.remove();
        } else {
            alert('Failed to delete image');
        }
    }).catch(() => alert('Network error'));
}
</script>
@endpush
@endsection
