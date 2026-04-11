@extends('layouts.admin')
@section('title', 'Banners')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-bold">Homepage Banners</h2>
    <a href="{{ route('admin.banners.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700">+ Add Banner</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($banners as $banner)
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 group hover:shadow-lg transition-shadow">
            <div class="aspect-video bg-gray-100 overflow-hidden"><img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"></div>
            <div class="p-4">
                <h4 class="font-bold text-gray-800 text-sm">{{ $banner->title }}</h4>
                <p class="text-xs text-gray-500 mt-1">{{ $banner->subtitle }}</p>
                <div class="flex items-center justify-between mt-3">
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $banner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</span>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="text-blue-600 text-xs font-semibold">Edit</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 text-xs font-semibold">Delete</button></form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
