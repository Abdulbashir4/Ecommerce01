<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'subcategory', 'brand'])
            ->where('status', true);

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->string('search') . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->integer('subcategory_id'));
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }
        if ($request->filled('price') && $request->integer('price') > 0) {
            $query->where('price', '<=', $request->integer('price'));
        }

        $products = $query->latest('product_id')->paginate(12)->withQueryString();
        $productDisplay = Setting::get('product.display', []);
        if (!is_array($productDisplay)) {
            $productDisplay = [];
        }

        $categories = Category::query()->orderBy('category_name')->get(['category_id','category_name']);
        $layoutSettings = [
            'container' => Setting::get('layout.container', '7xl'),
            'section_spacing' => Setting::get('layout.section_spacing', 'normal'),
            'sidebar' => Setting::get('layout.sidebar', 'right'),
            'shop_header' => Setting::get('layout.shop_header', true),
            'breadcrumbs' => Setting::get('layout.breadcrumbs', true),
            'pagination' => Setting::get('layout.pagination', true),
        ];

        return view('shop.index', compact('products', 'productDisplay', 'categories', 'layoutSettings'));
    }

    public function searchSuggestions(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        return response()->json(
            Product::query()
                ->where('status', true)
                ->where('product_name', 'like', '%' . $term . '%')
                ->orderBy('product_name')
                ->limit(10)
                ->get(['product_id', 'product_name'])
        );
    }

    public function show(string $slug)
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->orWhere('product_id', (int) $slug)
            ->firstOrFail();

        abort_unless($product->status, 404);
        $product->load(['category', 'subcategory', 'brand']);

        // Show products from the same category first, then fill the remaining
        // slots from the same subcategory/brand. The current product is excluded.
        $relatedQuery = Product::query()
            ->with(['category', 'subcategory', 'brand'])
            ->where('status', true)
            ->where('product_id', '!=', $product->product_id);

        if ($product->category_id) {
            $relatedQuery->where('category_id', $product->category_id);
        } elseif ($product->subcategory_id) {
            $relatedQuery->where('subcategory_id', $product->subcategory_id);
        }

        $relatedProducts = $relatedQuery
            ->orderByDesc('is_featured')
            ->orderByDesc('is_trending')
            ->latest('product_id')
            ->limit(8)
            ->get();

        // If the category has fewer than 8 products, supplement with products
        // from the same subcategory or brand without duplicating anything.
        if ($relatedProducts->count() < 8 && $product->brand_id) {
            $existingIds = $relatedProducts->pluck('product_id')->push($product->product_id);
            $extra = Product::query()
                ->with(['category', 'subcategory', 'brand'])
                ->where('status', true)
                ->where('brand_id', $product->brand_id)
                ->whereNotIn('product_id', $existingIds)
                ->latest('product_id')
                ->limit(8 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->concat($extra);
        }

        return view('product.show', compact('product', 'relatedProducts'));
    }
}
