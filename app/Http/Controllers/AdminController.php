<?php

namespace App\Http\Controllers;

use App\Models\{Product, Category, Subcategory, Brand, Order, CompanyInfo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Dashboard metrics are based only on the existing orders/products/users tables.
        $totalProducts = Product::count();
        $totalCustomers = DB::table('users')->where('is_admin', false)->count();

        $statusCounts = [
            'Pending' => Order::where('order_status', 'Pending')->count(),
            'Processing' => Order::where('order_status', 'Processing')->count(),
            'Shipped' => Order::where('order_status', 'Shipped')->count(),
            'Completed' => Order::where('order_status', 'Completed')->count(),
            'Cancelled' => Order::where('order_status', 'Cancelled')->count(),
        ];

        $runningOrders = $statusCounts['Processing'] + $statusCounts['Shipped'];

        $completedSales = (float) Order::where('order_status', 'Completed')->sum('total_amount');
        $paidIncome = (float) Order::where('order_status', 'Completed')
            ->where('payment_status', 'Paid')
            ->sum('total_amount');

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthlySales = (float) Order::where('order_status', 'Completed')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_amount');

        $monthlyOrders = Order::where('order_status', 'Completed')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $cards = [
            [
                'label' => 'Total Sales',
                'value' => '৳ '.number_format($completedSales, 2),
                'sub' => number_format($statusCounts['Completed']).' completed orders',
                'icon' => 'fa-solid fa-bangladeshi-taka-sign',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
            ],
            [
                'label' => 'Monthly Sales',
                'value' => '৳ '.number_format($monthlySales, 2),
                'sub' => number_format($monthlyOrders).' completed this month',
                'icon' => 'fa-solid fa-chart-line',
                'bg' => 'bg-indigo-50',
                'text' => 'text-indigo-600',
            ],
            [
                'label' => 'Paid Income',
                'value' => '৳ '.number_format($paidIncome, 2),
                'sub' => 'Completed & paid sales',
                'icon' => 'fa-solid fa-wallet',
                'bg' => 'bg-sky-50',
                'text' => 'text-sky-600',
            ],
            [
                'label' => 'Total Orders',
                'value' => number_format(array_sum($statusCounts)),
                'sub' => 'All order statuses',
                'icon' => 'fa-solid fa-cart-shopping',
                'bg' => 'bg-violet-50',
                'text' => 'text-violet-600',
            ],
        ];

        // 12-month completed-sales series. Group by year + month so January from different years never mixes.
        $monthlyRaw = Order::selectRaw("YEAR(created_at) as year_no, MONTH(created_at) as month_no, COALESCE(SUM(total_amount),0) as amount, COUNT(*) as orders")
            ->where('order_status', 'Completed')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year_no', 'month_no')
            ->orderBy('year_no')
            ->orderBy('month_no')
            ->get();

        $monthlyLookup = $monthlyRaw->keyBy(fn ($row) => $row->year_no.'-'.$row->month_no);
        $monthly = collect(range(11, 0))->reverse()->map(function ($offset) use ($monthlyLookup) {
            $date = now()->subMonths($offset);
            $key = $date->year.'-'.$date->month;
            $row = $monthlyLookup->get($key);

            return [
                'label' => $date->format('M'),
                'year' => $date->year,
                'amount' => (float) ($row->amount ?? 0),
                'orders' => (int) ($row->orders ?? 0),
            ];
        })->values();

        $maxMonthlySales = max(1, (float) $monthly->max('amount'));
        $monthly = $monthly->map(function ($month) use ($maxMonthlySales) {
            $month['height'] = $month['amount'] > 0
                ? max(8, round(($month['amount'] / $maxMonthlySales) * 100))
                : 4;
            return $month;
        });

        $recentOrders = Order::latest('order_id')->take(8)->get();

        // Low stock is shown at the existing product-stock level. The threshold is explicit in the UI.
        $lowStockThreshold = 5;
        $lowStockProducts = Product::query()
            ->where('status', true)
            ->where('stock_qty', '<=', $lowStockThreshold)
            ->orderBy('stock_qty')
            ->orderBy('product_name')
            ->take(8)
            ->get(['product_id', 'product_name', 'sku', 'stock_qty', 'stock_status', 'price']);

        $outOfStockCount = Product::where('status', true)
            ->where(function ($query) {
                $query->where('stock_qty', '<=', 0)->orWhere('stock_status', 'Out of Stock');
            })
            ->count();

        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.order_id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.product_id', '=', 'order_items.product_id')
            ->where('orders.order_status', 'Completed')
            ->selectRaw("COALESCE(products.product_name, order_items.product_name, 'Unknown Product') as product_name, SUM(order_items.qty) as qty, SUM(order_items.total) as amount")
            ->groupBy('order_items.product_id', 'products.product_name', 'order_items.product_name')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'cards',
            'statusCounts',
            'runningOrders',
            'recentOrders',
            'monthly',
            'lowStockProducts',
            'lowStockThreshold',
            'outOfStockCount',
            'topProducts',
            'completedSales',
            'paidIncome',
            'monthlySales',
            'monthlyOrders',
            'totalProducts',
            'totalCustomers'
        ));
    }

    public function products(Request $request)
    {
        $query = Product::query()->with(['category','brand']);
        if ($request->filled('q')) $query->where('product_name','like','%'.$request->string('q').'%');
        if ($request->filled('status') && in_array($request->status,['active','inactive'],true)) $query->where('status',$request->status==='active');
        return view('admin.products.index', ['products'=>$query->latest('product_id')->paginate(20)->withQueryString()]);
    }

    public function productCreate()
    {
        return view('admin.products.form', ['product'=>new Product(),'categories'=>Category::orderBy('category_name')->get(),'subcategories'=>Subcategory::orderBy('subcategory_name')->get(),'brands'=>Brand::orderBy('brand_name')->get()]);
    }

    public function productStore(Request $request)
    {
        $data = $this->productData($request);
        Product::create($data);
        return redirect('/admin/products')->with('success','Product created successfully.');
    }

    public function productEdit(Product $product)
    {
        return view('admin.products.form', ['product'=>$product,'categories'=>Category::orderBy('category_name')->get(),'subcategories'=>Subcategory::orderBy('subcategory_name')->get(),'brands'=>Brand::orderBy('brand_name')->get()]);
    }

    public function productUpdate(Request $request, Product $product)
    {
        $oldThumbnail = $product->thumbnail;
        $oldFeatured = $product->featured_image;

        $data = $this->productData($request, $product);
        $product->update($data);

        // Remove the old file only when a replacement image was actually uploaded.
        foreach (['thumbnail' => $oldThumbnail, 'featured_image' => $oldFeatured] as $field => $oldPath) {
            if (
                isset($data[$field]) &&
                $oldPath &&
                $oldPath !== $data[$field] &&
                str_starts_with($oldPath, 'uploads/products/')
            ) {
                $oldFile = public_path($oldPath);

                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
        }

        return redirect('/admin/products')->with('success','Product updated successfully.');
    }

    public function productDelete(Product $product)
    {
        $product->delete();
        return back()->with('success','Product deleted successfully.');
    }

    private function productData(Request $request, ?Product $product = null): array
    {
        $d = $request->validate([
            'product_name'=>['required','string','max:255'],'slug'=>['nullable','string','max:255'],'sku'=>['nullable','string','max:100'],
            'category_id'=>['nullable','integer'],'subcategory_id'=>['nullable','integer'],'brand_id'=>['nullable','integer'],
            'short_description'=>['nullable','string'],'long_description'=>['nullable','string'],'specifications'=>['nullable','string'],
            'price'=>['nullable','numeric','min:0'],'discount_price'=>['nullable','numeric','min:0'],'discount_percent'=>['nullable','integer','min:0','max:100'],
            'purchase_price'=>['nullable','numeric','min:0'],'stock_qty'=>['nullable','integer','min:0'],'stock_status'=>['required','in:In Stock,Out of Stock'],
            'min_order_qty'=>['nullable','integer','min:1'],'max_order_qty'=>['nullable','integer','min:1'],'weight'=>['nullable','numeric','min:0'],'dimensions'=>['nullable','string','max:255'],'shipping_cost'=>['nullable','numeric','min:0'],
            'meta_title'=>['nullable','string','max:255'],'meta_keywords'=>['nullable','string','max:255'],'meta_description'=>['nullable','string'],
            'thumbnail'=>['nullable','image','max:4096'],'featured_image'=>['nullable','image','max:4096'],
        ]);
        $d['slug'] = $d['slug'] ?: Str::slug($d['product_name']).'-'.time();
        foreach(['status','is_featured','is_trending','is_new','flash_sale'] as $f) $d[$f] = $request->boolean($f);
        foreach (['thumbnail', 'featured_image'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                // Always save product images inside the public web-accessible folder.
                $dir = public_path('uploads/products');

                if (! is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                // Generate a safe, unique filename so spaces/special characters
                // from the original upload can never break the image URL.
                $extension = strtolower($file->getClientOriginalExtension());
                $name = $field . '_' . Str::random(20) . '.' . $extension;

                $file->move($dir, $name);

                // Store the URL-relative path only once.
                $d[$field] = 'uploads/products/' . $name;
            } elseif ($product) {
                // Keep the existing image when editing without selecting a new file.
                unset($d[$field]);
            }
        }
        return $d;
    }

    public function orders(Request $request)
    {
        $query = Order::query();
        if($request->filled('status') && in_array($request->status,['Pending','Processing','Shipped','Completed','Cancelled'],true)) $query->where('order_status',$request->status);
        if($request->filled('q')) $query->where(function($q) use ($request){$q->where('customer_name','like','%'.$request->string('q').'%')->orWhere('phone','like','%'.$request->string('q').'%')->orWhere('order_id',$request->integer('q'));});
        return view('admin.orders.index',['orders'=>$query->latest('order_id')->paginate(30)->withQueryString()]);
    }

    public function orderShow(Order $order)
    {
        $order->load('items.product','user');
        return view('admin.orders.show',compact('order'));
    }

    public function orderUpdate(Request $request, $id)
    {
        $o=Order::findOrFail($id);
        $o->update($request->validate(['order_status'=>'required|in:Pending,Processing,Shipped,Completed,Cancelled','payment_status'=>'required|in:Unpaid,Paid,Refunded']));
        return back()->with('success','Order status updated successfully.');
    }

    public function catalog()
    {
        return view('admin.catalog.index', [
            'categories' => Category::withCount('products')->orderBy('category_name')->get(),
            'subcategories' => Subcategory::with(['category', 'parent'])->orderBy('category_id')->orderBy('parent_subcategory_id')->orderBy('subcategory_name')->get(),
            'brands' => Brand::with('subcategory.category')->orderBy('brand_name')->get(),
        ]);
    }

    public function categoryStore(Request $request){$d=$request->validate(['category_name'=>'required|string|max:255','category_image'=>'nullable|image|max:4096']);if($request->hasFile('category_image')){$dir=public_path('uploads/categories');if(!is_dir($dir))@mkdir($dir,0775,true);$name=uniqid('cat_').'.'.$request->file('category_image')->getClientOriginalExtension();$request->file('category_image')->move($dir,$name);$d['category_image']='uploads/categories/'.$name;}Category::create($d);return back()->with('success','Category added successfully.');}
    public function subcategoryStore(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'parent_subcategory_id' => ['nullable', 'integer', 'exists:subcategories,subcategory_id'],
            'subcategory_name' => ['required', 'string', 'max:255'],
        ]);

        if (!empty($data['parent_subcategory_id'])) {
            $parent = Subcategory::findOrFail($data['parent_subcategory_id']);
            if ((int) $parent->category_id !== (int) $data['category_id']) {
                return back()->withErrors(['parent_subcategory_id' => 'Parent subcategory must belong to the selected category.'])->withInput();
            }
        }

        Subcategory::create($data);
        return back()->with('success', 'Subcategory added successfully.');
    }
    public function brandStore(Request $request){Brand::create($request->validate(['subcategory_id'=>'required|integer|exists:subcategories,subcategory_id','brand_name'=>'required|string|max:255']));return back()->with('success','Brand added successfully.');}
    public function categoryDelete(Category $category){if($category->subcategories()->exists() || $category->products()->exists()) return back()->withErrors(['catalog'=>'This category cannot be deleted while it has subcategories or products.']);$category->delete();return back()->with('success','Category deleted successfully.');}
    public function subcategoryDelete(Subcategory $subcategory)
    {
        if ($subcategory->children()->exists() || $subcategory->brands()->exists() || Product::where('subcategory_id', $subcategory->subcategory_id)->exists()) {
            return back()->withErrors(['catalog' => 'This subcategory cannot be deleted while it has child subcategories, brands or products.']);
        }

        $subcategory->delete();
        return back()->with('success', 'Subcategory deleted successfully.');
    }
    public function brandDelete(Brand $brand){if(Product::where('brand_id',$brand->brand_id)->exists()) return back()->withErrors(['catalog'=>'This brand cannot be deleted while products are linked to it.']);$brand->delete();return back()->with('success','Brand deleted successfully.');}

    public function company()
    {
        return view('admin.company', ['company'=>CompanyInfo::query()->first() ?? new CompanyInfo()]);
    }

    public function companyUpdate(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'map_location' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string'],
            'facebook' => ['nullable', 'string', 'max:1000'],
            'youtube' => ['nullable', 'string', 'max:1000'],
            'about_us' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'slider_images' => ['nullable', 'array'],
            'slider_images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'right_slider_images' => ['nullable', 'array'],
            'right_slider_images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $company = CompanyInfo::query()->first() ?? new CompanyInfo();
        $company->fill(collect($data)->except([
            'logo', 'banner', 'slider_images', 'right_slider_images'
        ])->all());

        $dir = public_path('uploads/side_image');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $saveImage = function ($file, string $prefix) use ($dir): string {
            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $name = $prefix . '_' . Str::random(24) . '.' . $extension;
            $file->move($dir, $name);
            return $name;
        };

        if ($request->hasFile('logo')) {
            $company->logo = $saveImage($request->file('logo'), 'logo');
        }

        if ($request->hasFile('banner')) {
            $company->banner = $saveImage($request->file('banner'), 'banner');
        }

        $oldSlider = is_array($company->slider_image) ? $company->slider_image : [];
        if ($request->hasFile('slider_images')) {
            $newSlider = [];
            foreach ($request->file('slider_images', []) as $file) {
                $newSlider[] = $saveImage($file, 'slider');
            }
            $company->slider_image = array_values(array_merge($oldSlider, $newSlider));
        }

        $oldRight = is_array($company->right_slider) ? $company->right_slider : [];
        if ($request->hasFile('right_slider_images')) {
            $newRight = [];
            foreach ($request->file('right_slider_images', []) as $file) {
                $newRight[] = $saveImage($file, 'promo');
            }
            $company->right_slider = array_values(array_merge($oldRight, $newRight));
        }

        $company->save();

        return back()->with('success','Company information and images updated successfully.');
    }

    public function sales()
    {
        $products = Product::query()->where('status', 1)->where('stock_qty', '>', 0)->orderBy('product_name')->get(['product_id','product_name','price','stock_qty','sku']);
        $company = CompanyInfo::query()->first() ?? new CompanyInfo();
        return view('admin.sales', compact('products','company'));
    }

    public function saleStore(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['nullable','string','max:200'],
            'email' => ['nullable','email','max:200'],
            'phone' => ['nullable','string','max:100'],
            'address' => ['nullable','string','max:255'],
            'discount' => ['nullable','numeric','min:0'],
            'paid' => ['nullable','numeric','min:0'],
            'payment_method' => ['required','string','max:50'],
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:products,product_id'],
            'items.*.price' => ['required','numeric','min:0'],
            'items.*.qty' => ['required','integer','min:1'],
            'items.*.discount' => ['nullable','numeric','min:0'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;
            $totalItemDiscount = 0;
            $prepared = [];

            foreach ($data['items'] as $item) {
                $product = Product::where('product_id', $item['product_id'])->lockForUpdate()->firstOrFail();
                $qty = (int) $item['qty'];
                if (!$product->status || (int)$product->stock_qty < $qty) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        ['items' => "Insufficient stock for {$product->product_name}. Available: {$product->stock_qty}"]
                    ]);
                }

                $price = (float) $item['price'];
                $gross = $price * $qty;
                $itemDiscount = min((float)($item['discount'] ?? 0), $gross);
                $lineTotal = $gross - $itemDiscount;
                $subtotal += $gross;
                $totalItemDiscount += $itemDiscount;
                $prepared[] = [$product, $qty, $price, $lineTotal];
            }

            $invoiceDiscount = min((float)($data['discount'] ?? 0), max(0, $subtotal - $totalItemDiscount));
            $grandTotal = max(0, $subtotal - $totalItemDiscount - $invoiceDiscount);
            $paid = min((float)($data['paid'] ?? 0), $grandTotal);

            $order = Order::create([
                'customer_name' => $data['customer_name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'payment_method' => $data['payment_method'],
                'total_amount' => $grandTotal,
                'created_at' => now(),
                'order_status' => 'Completed',
                'payment_status' => $paid >= $grandTotal && $grandTotal > 0 ? 'Paid' : 'Unpaid',
                'user_id' => auth()->id(),
            ]);

            foreach ($prepared as [$product, $qty, $price, $lineTotal]) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'price' => $price,
                    'qty' => $qty,
                    'total' => $lineTotal,
                ]);
                $product->stock_qty = (int)$product->stock_qty - $qty;
                $product->stock_status = $product->stock_qty > 0 ? 'In Stock' : 'Out of Stock';
                $product->save();
            }

            return $order;
        });

        return redirect('/admin/sales/invoice/'.$order->order_id)->with('success','Sale created successfully.');
    }

    public function saleHistory()
    {
        return view('admin.sales-history', [
            'orders' => Order::where('order_status','Completed')->latest('order_id')->paginate(20),
        ]);
    }

    public function saleInvoice(Order $order)
    {
        $order->load('items','user');
        $company = CompanyInfo::query()->first() ?? new CompanyInfo();
        return view('admin.sale-invoice', compact('order','company'));
    }

    public function tracking(Request $request){$orders=Order::query()->when($request->filled('q'),fn($q)=>$q->where('order_id',$request->integer('q')))->latest('order_id')->paginate(20)->withQueryString();return view('admin.tracking',compact('orders'));}
}
