@extends('layouts.admin')
@section('title', 'CMS Pages')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-bold">All Pages</h2>
    <a href="{{ route('admin.pages.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700">+ Add Page</a>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="bg-gray-50 text-gray-500 text-left"><th class="px-6 py-4">Title</th><th class="px-6 py-4">Slug</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Updated</th><th class="px-6 py-4">Actions</th></tr></thead>
        <tbody>
            @foreach($pages as $page)
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $page->title }}</td>
                    <td class="px-6 py-4 text-gray-500">/page/{{ $page->slug }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-bold {{ $page->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $page->is_active ? 'Active' : 'Draft' }}</span></td>
                    <td class="px-6 py-4 text-gray-500">{{ $page->updated_at->diffForHumans() }}</td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="text-blue-600 text-xs font-semibold">Edit</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 text-xs font-semibold">Delete</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $pages->links() }}</div>
@endsection
