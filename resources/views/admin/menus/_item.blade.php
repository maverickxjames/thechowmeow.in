<div class="menu-item-wrapper" data-id="{{ $item->id }}" data-parent="{{ $item->parent_id ?? '' }}">
    <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-50 hover:bg-violet-50 transition-colors" style="margin-left: {{ $level * 20 }}px">
        <div class="flex items-center gap-2">
            <span class="drag-handle cursor-grab text-gray-400 select-none hover:text-gray-600" title="Drag to reorder">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm6 0a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM7 8a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm6 0a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM7 14a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm6 0a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>
            </span>
            <span class="text-sm font-medium text-gray-800">{{ $item->title }}</span>
            <span class="text-xs text-gray-400">({{ $item->type }})</span>
            @if(!$item->is_active)
                <span class="text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded">Hidden</span>
            @endif
        </div>
        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST">
            @csrf @method('DELETE')
            <button class="text-red-400 hover:text-red-600 text-xs font-semibold transition-colors">Remove</button>
        </form>
    </div>

    @if($item->children->count())
        <div class="children-sortable space-y-1 mt-1">
            @foreach($item->children as $child)
                @include('admin.menus._item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @else
        <div class="children-sortable" style="min-height: 4px; margin-left: {{ ($level + 1) * 20 }}px"></div>
    @endif
</div>
