<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Page;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(protected MenuService $menuService) {}

    public function index()
    {
        $menus = Menu::with(['allItems.children', 'allItems.linkable'])->get();
        $categories = Category::active()->orderBy('name')->get();
        $pages = Page::active()->orderBy('title')->get();
        return view('admin.menus.index', compact('menus', 'categories', 'pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|in:header,footer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $this->menuService->create($validated);

        return back()->with('success', 'Menu created.');
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'type' => 'required|in:custom,category,page',
            'linkable_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') || true;

        if ($validated['type'] === 'category' && !empty($validated['linkable_id'])) {
            $validated['linkable_type'] = Category::class;
        } elseif ($validated['type'] === 'page' && !empty($validated['linkable_id'])) {
            $validated['linkable_type'] = Page::class;
        }

        $validated['sort_order'] = MenuItem::where('menu_id', $validated['menu_id'])->max('sort_order') + 1;

        $this->menuService->createItem($validated);

        return back()->with('success', 'Menu item added.');
    }

    public function updateItem(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'type' => 'required|in:custom,category,page',
            'linkable_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['type'] === 'category' && !empty($validated['linkable_id'])) {
            $validated['linkable_type'] = Category::class;
        } elseif ($validated['type'] === 'page' && !empty($validated['linkable_id'])) {
            $validated['linkable_type'] = Page::class;
        }

        $this->menuService->updateItem($menuItem, $validated);

        return back()->with('success', 'Menu item updated.');
    }

    public function destroyItem(MenuItem $menuItem)
    {
        $this->menuService->deleteItem($menuItem);
        return back()->with('success', 'Menu item deleted.');
    }

    public function reorder(Request $request)
    {
        $this->menuService->reorderItems($request->input('items', []));
        return response()->json(['success' => true]);
    }

    public function destroy(Menu $menu)
    {
        $this->menuService->delete($menu);
        return back()->with('success', 'Menu deleted.');
    }
}
