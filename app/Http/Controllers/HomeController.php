<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CompanyInfo;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $company = CompanyInfo::query()->first();

        $categories = Category::query()
            ->with(['subcategories' => fn($q) => $q->whereNull('parent_subcategory_id')->with(['childrenRecursive', 'brands'])])
            ->orderBy('category_id')
            ->get();

        $query = Product::query()
            ->where('status', true)
            ->with(['category', 'subcategory', 'brand']);

        if ($request->filled('brand_id') && $request->integer('brand_id') > 0) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('category_id') && $request->integer('category_id') > 0) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('subcategory_id') && $request->integer('subcategory_id') > 0) {
            $query->where('subcategory_id', $request->integer('subcategory_id'));
        }

        if ($request->filled('price') && $request->integer('price') > 0) {
            $query->where('price', '<=', $request->integer('price'));
        }

        $productDisplay = Setting::get('product.display', []);
        if (!is_array($productDisplay)) {
            $productDisplay = [];
        }

        $homeLimit = (int) ($productDisplay['home_limit'] ?? 8);
        $products = $query->latest('product_id')->limit(max(1, min($homeLimit, 24)))->get();

        return view('home.index', compact('company', 'categories', 'products', 'productDisplay'));
    }
}
