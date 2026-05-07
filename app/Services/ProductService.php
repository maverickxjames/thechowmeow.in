<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected function productImageDisk(): string
    {
        return Setting::where('key', 'product_image_disk')->value('value') ?? 'r2';
    }
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
            $this->deleteImageFile($image->image_path);
        }
        
        // Explicitly detach relationships to prevent orphaned records
        // in case database-level cascade constraints are not enforced.
        $product->categories()->detach();
        $product->reviews()->delete();
        $product->variants()->delete();
        
        return $product->delete();
    }

    public function uploadImages(Product $product, array $files, bool $firstIsPrimary = false): void
    {
        $disk = $this->productImageDisk(); // reads from admin settings (r2 or public)
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                // Compress and resize image
                $image = $manager->read($file->getRealPath());
                $image->scaleDown(width: 1920);
                $encoded = $image->toWebp(quality: 80);

                $filename = Str::random(40) . '.webp';
                $path = 'products/' . $product->id . '/' . $filename;

                Storage::disk($disk)->put($path, $encoded->toString(), 'public');

                // For R2: store the full public URL so it remains valid even if disk setting changes.
                // For local public: store the relative path (standard behaviour).
                $storedPath = $disk === 'r2'
                    ? Storage::disk('r2')->url($path)
                    : $path;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $storedPath,
                    'alt_text'   => $product->name,
                    'sort_order' => $index,
                    'is_primary' => $firstIsPrimary && $index === 0 && $product->images()->count() === 0,
                ]);
            }
        }
    }

    public function deleteImage(ProductImage $image): bool
    {
        $this->deleteImageFile($image->image_path);
        return $image->delete();
    }

    /**
     * Delete a file from the correct disk.
     * If image_path is a full URL (R2), extract the object key and delete from R2.
     * If it's a relative path, delete from the public disk.
     */
    protected function deleteImageFile(string $imagePath): void
    {
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            // Strip the R2 public base URL to get the object key
            $r2BaseUrl = rtrim(config('filesystems.disks.r2.url', ''), '/');
            if ($r2BaseUrl && str_starts_with($imagePath, $r2BaseUrl)) {
                $objectKey = ltrim(substr($imagePath, strlen($r2BaseUrl)), '/');
                Storage::disk('r2')->delete($objectKey);
            }
        } else {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
