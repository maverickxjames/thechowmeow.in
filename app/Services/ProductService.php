<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function getProducts(array $filters = [], int $perPage = 12)
    {
        $query = Product::active()
            ->with(['primaryImage', 'images', 'activeVariants', 'categories', 'approvedReviews']);

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['min_price'])) {
            $query->whereHas('activeVariants', function ($q) use ($filters) {
                $q->where('price', '>=', $filters['min_price']);
            });
        }

        if (!empty($filters['max_price'])) {
            $query->whereHas('activeVariants', function ($q) use ($filters) {
                $q->where('price', '<=', $filters['max_price']);
            });
        }

        if (!empty($filters['featured'])) {
            $query->featured();
        }

        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        return $query->paginate($perPage);
    }

    public function getBySlug(string $slug): ?Product
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with(['images', 'activeVariants', 'categories', 'approvedReviews.user'])
            ->first();

        if ($product) {
            $product->increment('views_count');
        }

        return $product;
    }

    public function getFeatured(int $limit = 8)
    {
        return Product::active()->featured()
            ->with(['primaryImage', 'activeVariants'])
            ->limit($limit)
            ->get();
    }

    public function getBestSellers(int $limit = 8)
    {
        return Product::active()
            ->with(['primaryImage', 'activeVariants'])
            ->withCount(['reviews as order_count' => function ($q) {
                // Using orders through order_items would be better, but this approximates
            }])
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Product
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $product = Product::create($data);

        if (!empty($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $product->update($data);

        if (isset($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return $product;
    }

    public function delete(Product $product): bool
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        return $product->delete();
    }

    public function uploadImages(Product $product, array $files, bool $firstIsPrimary = false): void
    {
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('products/' . $product->id, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'sort_order' => $index,
                    'is_primary' => $firstIsPrimary && $index === 0 && $product->images()->count() === 0,
                ]);
            }
        }
    }

    public function deleteImage(ProductImage $image): bool
    {
        Storage::disk('public')->delete($image->image_path);
        return $image->delete();
    }
}
