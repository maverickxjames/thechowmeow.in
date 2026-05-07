<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    /**
     * Show the import page with upload form.
     */
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Preview the uploaded Excel file before importing.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        if (count($data) < 2) {
            return back()->with('error', 'The file appears to be empty or has no data rows.');
        }

        $headers = array_map(fn($h) => strtolower(trim($h ?? '')), $data[0]);
        $rows = array_slice($data, 1);

        // Parse each row into a structured preview
        $parsed = [];
        $skipped = 0;

        foreach ($rows as $i => $row) {
            $mapped = $this->mapRow($headers, $row);

            if (!$mapped['name']) {
                $skipped++;
                continue;
            }

            $parsed[] = [
                'row_number' => $i + 2, // Excel row (1-indexed + header)
                'name'       => $mapped['name'],
                'category'   => $mapped['category'],
                'sub_category' => $mapped['sub_category'],
                'sub_category_2' => $mapped['sub_category_2'],
                'color'      => $mapped['color'],
                'sizes'      => $mapped['sizes'],
                'quantities' => $mapped['quantities'],
                'gender'     => $mapped['gender'],
            ];
        }

        // Store file temporarily for the actual import
        $tempPath = $file->storeAs('imports', 'pending_import.' . $file->getClientOriginalExtension());

        return view('admin.import.index', [
            'preview' => $parsed,
            'skipped' => $skipped,
            'tempPath' => $tempPath,
            'filename' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Execute the actual import from the previously uploaded file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'temp_path'   => 'required|string',
            'base_price'  => 'required|numeric|min:0',
        ]);

        if (!Storage::exists($request->temp_path)) {
            return redirect()->route('admin.import.index')
                ->with('error', 'Upload expired. Please upload the file again.');
        }
        $fullPath = Storage::path($request->temp_path);

        $spreadsheet = IOFactory::load($fullPath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $headers = array_map(fn($h) => strtolower(trim($h ?? '')), $data[0]);
        $rows = array_slice($data, 1);

        $basePrice = (float) $request->base_price;
        $imported = 0;
        $variantsCreated = 0;
        $categoriesCreated = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $mapped = $this->mapRow($headers, $row);

            if (!$mapped['name']) {
                continue; // skip empty rows
            }

            try {
                // 1. Resolve/Create categories
                $categoryIds = $this->resolveCategories(
                    $mapped['category'],
                    $mapped['sub_category'],
                    $mapped['sub_category_2'],
                    $categoriesCreated
                );

                // 2. Create or find product (by name)
                $product = Product::firstOrCreate(
                    ['slug' => Str::slug($mapped['name'])],
                    [
                        'name'              => $mapped['name'],
                        'base_price'        => $basePrice,
                        'is_active'         => true,
                        'short_description' => trim(($mapped['gender'] ? ucfirst($mapped['gender']) . "'s " : '') . $mapped['sub_category']),
                    ]
                );

                // 3. Sync categories
                if (!empty($categoryIds)) {
                    $product->categories()->syncWithoutDetaching($categoryIds);
                }

                // 4. Create variants (size × color combinations)
                $sizes = $mapped['sizes'];
                $quantities = $mapped['quantities'];
                $color = $mapped['color'];

                foreach ($sizes as $si => $size) {
                    $size = trim($size);
                    if (!$size) continue;

                    $qty = isset($quantities[$si]) ? max(0, (int) trim($quantities[$si])) : 0;
                    $sku = strtoupper(Str::slug($product->name . '-' . $color . '-' . $size, '-'));

                    // Avoid duplicate variants
                    $existing = ProductVariant::where('product_id', $product->id)
                        ->where('size', $size)
                        ->where('color', $color)
                        ->first();

                    if ($existing) {
                        // Update stock if variant already exists
                        $existing->update(['stock_quantity' => $qty]);
                    } else {
                        ProductVariant::create([
                            'product_id'     => $product->id,
                            'size'           => $size,
                            'color'          => $color,
                            'sku'            => $this->generateUniqueSku($sku),
                            'price'          => $basePrice,
                            'stock_quantity' => $qty,
                            'is_active'      => true,
                        ]);
                        $variantsCreated++;
                    }
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($i + 2) . " ({$mapped['name']}): " . $e->getMessage();
            }
        }

        // Clean up temp file
        Storage::delete($request->temp_path);

        return redirect()->route('admin.import.index')->with('result', [
            'imported'   => $imported,
            'variants'   => $variantsCreated,
            'categories' => $categoriesCreated,
            'errors'     => $errors,
        ]);
    }

    /**
     * Download a sample Excel template.
     */
    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['S.No.', 'Product name', 'Category', 'Sub Category', 'Sub Category_2', 'Color', 'Size', 'Quantity', 'Gender'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8E0F5');

        // Sample data
        $sample = [
            [1, 'Fruit and Smiley Shirt', 'Cat', 'Shirt', 'Summer Wear', 'Green', 'XXS | XS', '1 | 2', 'Male'],
            [2, 'Rainbow Stripes Dress', 'Dog', 'Dress', 'Summer Wear', 'White', 'Small | Medium | Large', '2 | 3 | 1', 'Female'],
        ];
        $sheet->fromArray($sample, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'product_import_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────

    /**
     * Map a raw Excel row to a named array using detected headers.
     */
    private function mapRow(array $headers, array $row): array
    {
        $get = function (string ...$keys) use ($headers, $row) {
            foreach ($keys as $key) {
                $idx = array_search($key, $headers);
                if ($idx !== false && isset($row[$idx]) && trim($row[$idx]) !== '') {
                    return trim($row[$idx]);
                }
            }
            return '';
        };

        $sizesRaw = $get('size', 'sizes');
        $quantitiesRaw = $get('quantity', 'quantities', 'qty');

        return [
            'name'           => $get('product name', 'name', 'product_name'),
            'category'       => $get('category'),
            'sub_category'   => $get('sub category', 'sub_category', 'subcategory'),
            'sub_category_2' => $get('sub category_2', 'sub_category_2', 'subcategory_2', 'sub category 2'),
            'color'          => $get('color', 'colour'),
            'sizes'          => array_map('trim', explode('|', $sizesRaw)),
            'quantities'     => array_map('trim', explode('|', $quantitiesRaw)),
            'gender'         => $get('gender'),
        ];
    }

    /**
     * Resolve or create a category hierarchy and return all IDs.
     */
    private function resolveCategories(string $main, string $sub, string $sub2, int &$createdCount): array
    {
        $ids = [];

        if (!$main) return $ids;

        // Main category (root)
        $mainCat = Category::firstOrCreate(
            ['slug' => Str::slug($main)],
            ['name' => $main, 'is_active' => true]
        );
        if ($mainCat->wasRecentlyCreated) $createdCount++;
        $ids[] = $mainCat->id;

        // Sub category (child of main)
        if ($sub) {
            $subCat = Category::firstOrCreate(
                ['slug' => Str::slug($main . '-' . $sub)],
                ['name' => $sub, 'parent_id' => $mainCat->id, 'is_active' => true]
            );
            if ($subCat->wasRecentlyCreated) $createdCount++;
            $ids[] = $subCat->id;
        }

        // Sub category 2 (child of sub)
        if ($sub2 && $sub) {
            $sub2Cat = Category::firstOrCreate(
                ['slug' => Str::slug($main . '-' . $sub . '-' . $sub2)],
                ['name' => $sub2, 'parent_id' => $subCat->id ?? $mainCat->id, 'is_active' => true]
            );
            if ($sub2Cat->wasRecentlyCreated) $createdCount++;
            $ids[] = $sub2Cat->id;
        }

        return $ids;
    }

    /**
     * Generate a unique SKU, appending a suffix if needed.
     */
    private function generateUniqueSku(string $base): string
    {
        $sku = $base;
        $suffix = 1;
        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $base . '-' . $suffix++;
        }
        return $sku;
    }
}
