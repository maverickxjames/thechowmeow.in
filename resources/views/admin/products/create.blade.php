@extends('layouts.admin')
@section('title', 'Add Product')
@section('subtitle', 'Create a new product listing')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-6" id="productForm">
    @csrf

    {{-- Product Details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        <h3 class="font-bold text-gray-900">Product Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Name <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Auto-generated from name"
                       class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Short Description</label>
                <textarea name="short_description" rows="2" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ old('short_description') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Description</label>
                <textarea name="description" rows="5" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Base Price <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₹</span>
                    <input type="number" step="0.01" name="base_price" value="{{ old('base_price', 0) }}" required
                           class="w-full pl-7 rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
            </div>
            <div class="flex items-center gap-6 pt-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded text-violet-600 focus:ring-violet-200">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
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
                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
                           class="rounded text-violet-600 focus:ring-violet-200">
                    <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Images - Drag & Drop --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4" x-data="imageUploader()">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900">Product Images</h3>
                <p class="text-xs text-gray-400 mt-0.5">First image will be set as primary. Drag to reorder. Max 2MB each.</p>
            </div>
            <span class="text-xs font-medium text-gray-400" x-text="files.length + ' image(s)'"></span>
        </div>

        {{-- Drop Zone --}}
        <div @dragover.prevent="dragging = true"
             @dragleave.prevent="dragging = false"
             @drop.prevent="handleDrop($event)"
             @click="$refs.fileInput.click()"
             :class="dragging ? 'border-violet-400 bg-violet-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
             class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all">
            <div class="flex flex-col items-center gap-2">
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Drag & drop images here</p>
                <p class="text-xs text-gray-400">or click to browse • PNG, JPG, WEBP</p>
            </div>
        </div>
        <input type="file" x-ref="fileInput" @change="handleFiles($event)" multiple accept="image/*" class="hidden">

        {{-- Preview Grid (sortable) --}}
        <div x-show="files.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3" x-ref="previewGrid">
            <template x-for="(file, index) in files" :key="file.id">
                <div class="relative group aspect-square rounded-xl overflow-hidden border-2 transition-all cursor-grab active:cursor-grabbing"
                     :class="index === 0 ? 'border-violet-400 ring-2 ring-violet-100' : 'border-gray-200'"
                     draggable="true"
                     @dragstart="dragStart(index, $event)"
                     @dragover.prevent="dragOver(index, $event)"
                     @dragend="dragEnd()"
                     :data-index="index">
                    <img :src="file.preview" class="w-full h-full object-cover">

                    {{-- Primary badge --}}
                    <div x-show="index === 0" class="absolute top-1.5 left-1.5">
                        <span class="px-1.5 py-0.5 bg-violet-600 text-white text-[10px] font-bold rounded">PRIMARY</span>
                    </div>

                    {{-- Order number --}}
                    <div class="absolute top-1.5 right-1.5">
                        <span class="w-5 h-5 flex items-center justify-center bg-black/50 text-white text-[10px] font-bold rounded-full" x-text="index + 1"></span>
                    </div>

                    {{-- Remove button --}}
                    <button @click.stop="removeFile(index)" type="button"
                            class="absolute bottom-1.5 right-1.5 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                        ×
                    </button>

                    {{-- Drag hint --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-70 drop-shadow transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    </div>
                </div>
            </template>
        </div>

        {{-- Hidden file inputs for form submission --}}
        <div x-ref="hiddenInputs"></div>
    </div>

    {{-- SEO --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h3 class="font-bold text-gray-900">SEO</h3>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                   class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Meta Description</label>
            <textarea name="meta_description" rows="2" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ old('meta_description') }}</textarea>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">
            Create Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors text-sm">
            Cancel
        </a>
    </div>
</form>

@push('scripts')
<script>
function imageUploader() {
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

        // Drag & drop reordering
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
</script>
@endpush
@endsection
