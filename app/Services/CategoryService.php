<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function getTree()
    {
        return Cache::remember('categories.tree', 3600, function () {
            return Category::active()
                ->roots()
                ->with('allChildren')
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function getAllCategories()
    {
        return Cache::remember('categories.all', 3600, function () {
            return Category::active()->orderBy('sort_order')->get();
        });
    }

    public function getBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)
            ->active()
            ->with(['children.children', 'products.images', 'products.activeVariants'])
            ->first();
    }

    public function create(array $data): Category
    {
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $data['image']->store('categories', 'public');
        }
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = Category::create($data);
        $this->clearCache();
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['image']) && $data['image']) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category->update($data);
        $this->clearCache();
        return $category;
    }

    public function delete(Category $category): bool
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $result = $category->delete();
        $this->clearCache();
        return $result;
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            Category::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'] ?? 0,
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget('categories.tree');
        Cache::forget('categories.all');
    }
}
