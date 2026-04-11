@extends('layouts.admin')
@section('title', 'Menu Builder')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const sortableInstances = [];

    // Debounce to avoid flooding on rapid drags
    let saveTimer = null;
    function scheduleReorder(menuId) {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => doReorder(menuId), 400);
    }

    function collectItems(container, parentId, items) {
        Array.from(container.children).forEach((el, index) => {
            if (!el.dataset.id) return;
            const id = parseInt(el.dataset.id);
            items.push({ id, sort_order: index, parent_id: parentId });
            const childList = el.querySelector(':scope > .children-sortable');
            if (childList) collectItems(childList, id, items);
        });
    }

    function doReorder(menuId) {
        const root = document.querySelector('#menu-' + menuId);
        if (!root) return;
        const items = [];
        collectItems(root, null, items);

        fetch('{{ route('admin.menus.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items })
        })
        .then(r => r.json())
        .then(() => {
            const toast = document.getElementById('reorder-toast');
            if (toast) { toast.classList.remove('hidden'); setTimeout(() => toast.classList.add('hidden'), 2000); }
        })
        .catch(console.error);
    }

    function initSortable(el, menuId) {
        return Sortable.create(el, {
            group: { name: 'menu-' + menuId, pull: true, put: true },
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'bg-violet-50',
            chosenClass: 'opacity-50',
            onEnd: () => scheduleReorder(menuId)
        });
    }

    function initMenu(menuId) {
        const root = document.querySelector('#menu-' + menuId);
        if (!root) return;

        // Init root level
        initSortable(root, menuId);

        // Init all children-sortable containers
        root.querySelectorAll('.children-sortable').forEach(el => {
            initSortable(el, menuId);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        @foreach($menus as $menu)
            initMenu({{ $menu->id }});
        @endforeach
    });
})();
</script>
@endpush

@section('content')

{{-- Reorder toast --}}
<div id="reorder-toast" class="hidden fixed bottom-6 right-6 z-50 bg-gray-900 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-lg">
    Order saved
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Panel --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Create Menu --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Create Menu</h3>
            <form action="{{ route('admin.menus.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="name" placeholder="Menu Name" required
                       class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
                <select name="location"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                    <option value="header">Header</option>
                    <option value="footer">Footer</option>
                </select>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-violet-600">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <button type="submit"
                        class="w-full px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Create Menu
                </button>
            </form>
        </div>

        {{-- Add Menu Item --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Add Menu Item</h3>
            <form action="{{ route('admin.menu-items.store') }}" method="POST" class="space-y-3"
                  x-data="{ type: 'custom' }">
                @csrf
                <select name="menu_id" required
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                    <option value="">Select Menu</option>
                    @foreach($menus as $menu)
                        <option value="{{ $menu->id }}">{{ $menu->name }} ({{ $menu->location }})</option>
                    @endforeach
                </select>

                <input type="text" name="title" placeholder="Item Label" required
                       class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">

                <select name="type" x-model="type"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                    <option value="custom">Custom Link</option>
                    <option value="category">Category</option>
                    <option value="page">Page</option>
                </select>

                <div x-show="type === 'custom'">
                    <input type="text" name="url" placeholder="URL (e.g. /shop)"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
                </div>

                <div x-show="type === 'category'" style="display:none">
                    <select name="linkable_id"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="type === 'page'" style="display:none">
                    <select name="linkable_id"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                        <option value="">Select Page</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}">{{ $page->title }}</option>
                        @endforeach
                    </select>
                </div>

                <select name="parent_id"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                    <option value="">No Parent (Top Level)</option>
                    @foreach($menus as $menu)
                        @foreach($menu->allItems as $item)
                            <option value="{{ $item->id }}">{{ $menu->name }} → {{ $item->title }}</option>
                        @endforeach
                    @endforeach
                </select>

                <button type="submit"
                        class="w-full px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors">
                    Add Item
                </button>
            </form>
        </div>
    </div>

    {{-- Menu Items --}}
    <div class="lg:col-span-2 space-y-6">
        @forelse($menus as $menu)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $menu->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Location: <span class="font-medium">{{ ucfirst($menu->location) }}</span>
                            &nbsp;·&nbsp;
                            <span class="{{ $menu->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $menu->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST"
                          onsubmit="return confirm('Delete entire menu?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 text-xs font-semibold hover:text-red-700 transition-colors">Delete Menu</button>
                    </form>
                </div>

                @if($menu->allItems->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-6">No items yet. Add items from the left panel.</p>
                @else
                    <p class="text-xs text-gray-400 mb-3">Drag items to reorder. Changes save automatically.</p>
                    <div class="space-y-1" id="menu-{{ $menu->id }}">
                        @foreach($menu->allItems->where('parent_id', null) as $item)
                            @include('admin.menus._item', ['item' => $item, 'level' => 0])
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <p class="text-sm font-medium text-gray-500">No menus created yet</p>
                <p class="text-xs text-gray-400 mt-1">Create your first menu from the left panel</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
