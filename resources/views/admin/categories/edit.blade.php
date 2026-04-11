@extends('layouts.admin')
@section('title', 'Edit Category: ' . $category->name)

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-6">
        @csrf @method('PUT')
        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-lg border-gray-200 focus:border-purple-400">
        </div>
        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full rounded-lg border-gray-200 focus:border-purple-400">
        </div>
        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Parent Category</label>
            <select name="parent_id" class="w-full rounded-lg border-gray-200 focus:border-purple-400">
                <option value="">None (Top Level)</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-200 focus:border-purple-400">{{ old('description', $category->description) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Image</label>
            @if($category->image)<img src="{{ asset('storage/' . $category->image) }}" class="w-20 h-20 rounded-lg object-cover mb-2">@endif
            <input type="file" name="image" accept="image/*" class="w-full rounded-lg border-gray-200 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full rounded-lg border-gray-200"></div>
            <div class="flex items-center pt-6"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded text-purple-600"><span class="text-sm font-medium text-gray-700">Active</span></label></div>
        </div>
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Meta Title</label><input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" class="w-full rounded-lg border-gray-200"></div>
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Meta Description</label><textarea name="meta_description" rows="2" class="w-full rounded-lg border-gray-200">{{ old('meta_description', $category->meta_description) }}</textarea></div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700">Update Category</button>
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
