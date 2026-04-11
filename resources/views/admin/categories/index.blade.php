@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-bold">All Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 transition-colors">+ Add Category</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-gray-500 text-left"><th class="px-6 py-4">Name</th><th class="px-6 py-4">Slug</th><th class="px-6 py-4">Parent</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Order</th><th class="px-6 py-4">Actions</th></tr></thead>
            <tbody>
                @foreach($categories as $category)
                    @include('admin.categories._row', ['category' => $category, 'level' => 0])
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
