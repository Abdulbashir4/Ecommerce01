<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Brand, Category, Product, Subcategory};
use App\Services\ProductImporter\ProductImportService;
use Illuminate\Http\Request;
use Throwable;

class ProductImporterController extends Controller
{
    public function __construct(private readonly ProductImportService $importer) {}

    public function index()
    {
        return view('admin.product-importer.index', [
            'categories' => Category::orderBy('category_name')->get(),
            'subcategories' => Subcategory::orderBy('subcategory_name')->get(),
            'brands' => Brand::orderBy('brand_name')->get(),
        ]);
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        try {
            return response()->json([
                'ok' => true,
                'product' => $this->importer->previewAmazon($data['url']),
            ]);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function import(Request $request)
    {
        // Browsers may submit a number input such as 0.00/10.00.
        // The products table stores discount_percent as an integer, so normalize
        // whole-number decimal input before Laravel's integer validation runs.
        $discountPercent = $request->input('discount_percent');
        if ($discountPercent !== null && $discountPercent !== '' && is_numeric($discountPercent)) {
            $numericDiscountPercent = (float) $discountPercent;
            if (fmod($numericDiscountPercent, 1.0) === 0.0) {
                $request->merge(['discount_percent' => (int) $numericDiscountPercent]);
            }
        }

        $data = $request->validate([
            'source_url' => ['required', 'url', 'max:2048'],
            'product_name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'long_description' => ['nullable', 'string', 'max:50000'],
            'specifications' => ['nullable', 'string', 'max:50000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'stock_status' => ['required', 'in:In Stock,Out of Stock'],
            'category_id' => ['nullable', 'integer', 'exists:categories,category_id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:subcategories,subcategory_id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,brand_id'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'thumbnail_url' => ['required', 'url', 'max:2048'],
            'gallery_urls' => ['nullable', 'array', 'max:11'],
            'gallery_urls.*' => ['url', 'max:2048'],
            'source_product_id' => ['nullable', 'string', 'max:150'],
        ]);

        try {
            $product = $this->importer->importDemo($data);

            return redirect()->route('admin.product-importer.index')
                ->with('success', 'Demo product সফলভাবে তৈরি হয়েছে: ' . $product->product_name)
                ->with('imported_product_id', $product->product_id);
        } catch (Throwable $e) {
            return back()->withInput()->withErrors(['source_url' => $e->getMessage()]);
        }
    }
}
