<tr class="border-b border-gray-50 hover:bg-gray-50">
    <td class="px-6 py-4"><span style="padding-left: {{ $level * 24 }}px" class="flex items-center gap-2">@if($level > 0) └ @endif <span class="font-medium text-gray-800">{{ $category->name }}</span></span></td>
    <td class="px-6 py-4 text-gray-500">{{ $category->slug }}</td>
    <td class="px-6 py-4 text-gray-500">{{ $category->parent?->name ?? '—' }}</td>
    <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-bold {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td class="px-6 py-4 text-gray-500">{{ $category->sort_order }}</td>
    <td class="px-6 py-4 flex gap-2">
        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">Edit</a>
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</button></form>
    </td>
</tr>
@if($category->children->count())
    @foreach($category->children as $child)
        @include('admin.categories._row', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
