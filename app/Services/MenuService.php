<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    public function getMenu(string $location = 'header')
    {
        return Cache::remember("menu.{$location}", 3600, function () use ($location) {
            return Menu::active()
                ->byLocation($location)
                ->with([
                    'items.linkable',
                    'items.children.linkable',
                    'items.children.children.linkable',
                ])
                ->first();
        });
    }

    public function create(array $data): Menu
    {
        $menu = Menu::create($data);
        $this->clearCache();
        return $menu;
    }

    public function update(Menu $menu, array $data): Menu
    {
        $menu->update($data);
        $this->clearCache();
        return $menu;
    }

    public function delete(Menu $menu): bool
    {
        $result = $menu->delete();
        $this->clearCache();
        return $result;
    }

    public function createItem(array $data): MenuItem
    {
        $item = MenuItem::create($data);
        $this->clearCache();
        return $item;
    }

    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        $item->update($data);
        $this->clearCache();
        return $item;
    }

    public function deleteItem(MenuItem $item): bool
    {
        $result = $item->delete();
        $this->clearCache();
        return $result;
    }

    public function reorderItems(array $items): void
    {
        foreach ($items as $itemData) {
            MenuItem::where('id', $itemData['id'])->update([
                'sort_order' => $itemData['sort_order'] ?? 0,
                'parent_id' => $itemData['parent_id'] ?? null,
            ]);
        }
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget('menu.header');
        Cache::forget('menu.footer');
    }
}
