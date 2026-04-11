@extends('layouts.admin')
@section('title', 'Add Banner')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-6">
        @csrf
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Title *</label><input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border-gray-200"></div>
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Subtitle</label><input type="text" name="subtitle" value="{{ old('subtitle') }}" class="w-full rounded-lg border-gray-200"></div>
        <div><label class="text-sm font-semibold text-gray-700 block mb-1">Image *</label><input type="file" name="image" accept="image/*" required class="w-full rounded-lg border-gray-200 text-sm"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Button Text</label><input type="text" name="button_text" value="{{ old('button_text') }}" class="w-full rounded-lg border-gray-200"></div>
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Button URL</label><input type="text" name="button_url" value="{{ old('button_url') }}" class="w-full rounded-lg border-gray-200"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold text-gray-700 block mb-1">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded-lg border-gray-200"></div>
            <div class="flex items-center pt-6"><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded text-purple-600"><span class="text-sm">Active</span></label></div>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700">Create Banner</button>
    </form>
</div>
@endsection
