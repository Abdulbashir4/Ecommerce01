<?php

namespace App\Http\Controllers;

use App\Models\{Product, Category, Subcategory, Brand, Order, CompanyInfo, Setting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
                'value' => Setting::get('general.currency_symbol', '৳').' '.number_format($completedSales, 2),
                'sub' => number_format($statusCounts['Completed']).' completed orders',
                'icon' => 'fa-solid fa-bangladeshi-taka-sign',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
            ],
            [
                'label' => 'Monthly Sales',
                'value' => Setting::get('general.currency_symbol', '৳').' '.number_format($monthlySales, 2),
                'sub' => number_format($monthlyOrders).' completed this month',
                'icon' => 'fa-solid fa-chart-line',
                'bg' => 'bg-indigo-50',
                'text' => 'text-indigo-600',
            ],
            [
                'label' => 'Paid Income',
                'value' => Setting::get('general.currency_symbol', '৳').' '.number_format($paidIncome, 2),
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

    /**
     * Product main-image graphics editor.
     */
    public function productImageEditor(Request $request)
    {
        $query = Product::query()->select(['product_id', 'product_name', 'sku', 'thumbnail', 'featured_image']);
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('product_name', 'like', '%'.$term.'%')
                    ->orWhere('sku', 'like', '%'.$term.'%');
            });
        }

        $products = $query->orderBy('product_name')->paginate(30)->withQueryString();
        $selectedProduct = null;
        if ($request->filled('product')) {
            $selectedProduct = Product::find($request->integer('product'));
        }

        return view('admin.products.image-editor', compact('products', 'selectedProduct'));
    }

    /**
     * Store a browser-rendered final image as the product's main image.
     * The browser performs crop/resize/padding/centering and visual adjustments
     * on a canvas; the server only accepts a generated image payload and safely
     * replaces the existing main/thumbnail image after the new file is stored.
     */
    public function productImageEditorSave(Request $request, Product $product)
    {
        $data = $request->validate([
            'image' => ['required', 'string'],
            'width' => ['required', 'integer', 'min:200', 'max:5000'],
            'height' => ['required', 'integer', 'min:200', 'max:5000'],
        ]);

        if (!preg_match('#^data:image/(webp|png|jpe?g);base64,#i', $data['image'], $matches)) {
            return back()->withErrors(['image' => 'Invalid generated image format. Please export the image again.']);
        }

        $binary = base64_decode(substr($data['image'], strpos($data['image'], ',') + 1), true);
        if ($binary === false || strlen($binary) < 100) {
            return back()->withErrors(['image' => 'The generated image could not be decoded.']);
        }

        // Keep uploads bounded even though the browser already resized the canvas.
        if (strlen($binary) > 15 * 1024 * 1024) {
            return back()->withErrors(['image' => 'The generated image is too large. Please use a smaller output size.']);
        }

        $extension = strtolower($matches[1]);
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        // Validate the decoded bytes as a real raster image before storing them.
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->buffer($binary);
        $allowedMimes = [
            'image/webp' => 'webp',
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
        ];
        if (!isset($allowedMimes[$detectedMime])) {
            return back()->withErrors(['image' => 'The generated payload is not a valid PNG, JPEG or WebP image.']);
        }
        $extension = $allowedMimes[$detectedMime];

        $dir = public_path('uploads/products');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return back()->withErrors(['image' => 'Product image directory could not be created.']);
        }

        $name = 'main_' . Str::random(24) . '.' . $extension;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $name;
        if (@file_put_contents($fullPath, $binary) === false) {
            return back()->withErrors(['image' => 'The new product image could not be saved.']);
        }

        $newPath = 'uploads/products/' . $name;
        $oldPaths = array_unique(array_filter([
            $product->thumbnail,
            $product->featured_image,
        ]));

        try {
            $product->update([
                'thumbnail' => $newPath,
                'featured_image' => $newPath,
            ]);
        } catch (\Throwable $e) {
            @unlink($fullPath);
            throw $e;
        }

        foreach ($oldPaths as $oldPath) {
            if ($oldPath !== $newPath && !$this->isProductImageStillReferenced($oldPath)) {
                $this->deleteProductImage($oldPath);
            }
        }

        return redirect()->route('admin.products.image-editor', ['product' => $product->product_id])
            ->with('success', 'Product main image updated successfully. The previous main image was removed.');
    }

    /**
     * Do not remove an old main image if another product field (including a
     * gallery) still references the same physical file.
     */
    private function isProductImageStillReferenced(string $path): bool
    {
        $needle = trim(str_replace('\\', '/', $path));
        if ($needle === '') {
            return false;
        }

        $needle = ltrim($needle, '/');
        if (str_starts_with($needle, 'uploads/products/')) {
            $filename = substr($needle, strlen('uploads/products/'));
        } else {
            $filename = basename($needle);
        }
        if ($filename === '') {
            return false;
        }

        $direct = Product::query()
            ->where('thumbnail', $path)
            ->orWhere('featured_image', $path)
            ->exists();
        if ($direct) {
            return true;
        }

        // Gallery JSON formats have changed over the lifetime of the project,
        // so inspect decoded gallery arrays instead of relying on DB-specific JSON SQL.
        $found = false;
        Product::query()->select(['product_id', 'gallery_images'])->chunkById(500, function ($products) use ($filename, &$found): bool {
            foreach ($products as $product) {
                foreach ((array) ($product->gallery_images ?? []) as $galleryImage) {
                    $values = is_string($galleryImage)
                        ? [$galleryImage]
                        : (is_array($galleryImage) ? array_values($galleryImage) : []);
                    foreach ($values as $value) {
                        if (!is_string($value) || trim($value) === '') {
                            continue;
                        }
                        $normalized = ltrim(str_replace('\\', '/', trim($value)), '/');
                        if (basename(parse_url($normalized, PHP_URL_PATH) ?: $normalized) === $filename) {
                            $found = true;
                            return false;
                        }
                    }
                }
            }
            return true;
        }, 'product_id');

        return $found;
    }

    /**
     * Show product images that are no longer referenced by any product record.
     */
    public function productImageCleanup()
    {
        $scan = $this->findUnusedProductImages();

        return view('admin.products.image-cleanup', [
            'files' => $scan['files'],
            'totalSize' => $scan['totalSize'],
            'referencedCount' => $scan['referencedCount'],
            'directory' => public_path('uploads/products'),
        ]);
    }

    /**
     * Permanently remove product image files that are not referenced by any
     * product's thumbnail, featured_image or gallery_images field.
     */
    public function productImageCleanupRun(Request $request)
    {
        $scan = $this->findUnusedProductImages();
        $deleted = 0;
        $failed = 0;
        $deletedBytes = 0;

        foreach ($scan['files'] as $file) {
            $fullPath = $file['full_path'];
            if (!is_file($fullPath)) {
                continue;
            }

            if (@unlink($fullPath)) {
                $deleted++;
                $deletedBytes += $file['size'];
            } else {
                $failed++;
            }
        }

        $message = $deleted
            ? $deleted.' unused product image'.($deleted === 1 ? '' : 's').' deleted ('.number_format($deletedBytes / 1024, 1).' KB).
'
            : 'No unused product images were found.';

        if ($failed) {
            $message .= ' '.$failed.' file'.($failed === 1 ? '' : 's').' could not be deleted.';
        }

        return redirect()->route('admin.products.image-cleanup')->with('success', trim($message));
    }

    /**
     * Build a safe inventory of unreferenced files inside public/uploads/products.
     * Only files in this directory are considered; no other public/storage files
     * are touched.
     */
    private function findUnusedProductImages(): array
    {
        $directory = public_path('uploads/products');
        $referenced = [];

        $addReference = function ($value) use (&$referenced): void {
            if (!is_string($value) || trim($value) === '') {
                return;
            }

            $value = trim(str_replace('\\', '/', $value));
            if (preg_match('#^(https?:)?//#i', $value)) {
                return;
            }

            $value = ltrim($value, '/');
            $queryPos = strpos($value, '?');
            if ($queryPos !== false) {
                $value = substr($value, 0, $queryPos);
            }
            $hashPos = strpos($value, '#');
            if ($hashPos !== false) {
                $value = substr($value, 0, $hashPos);
            }

            if (str_starts_with($value, 'storage/')) {
                return;
            }
            if (str_starts_with($value, 'public/')) {
                $value = substr($value, 7);
            }
            if (str_starts_with($value, 'uploads/products/')) {
                $value = substr($value, strlen('uploads/products/'));
            } elseif (str_contains($value, 'uploads/products/')) {
                $value = substr($value, strpos($value, 'uploads/products/') + strlen('uploads/products/'));
            } else {
                $value = basename($value);
            }

            $value = ltrim($value, '/');
            if ($value !== '' && $value !== '.' && $value !== '..') {
                $referenced[$value] = true;
            }
        };

        Product::query()->select(['product_id', 'thumbnail', 'featured_image', 'gallery_images'])->chunkById(500, function ($products) use ($addReference): void {
            foreach ($products as $product) {
                $addReference($product->thumbnail);
                $addReference($product->featured_image);

                foreach ((array) ($product->gallery_images ?? []) as $galleryImage) {
                    if (is_string($galleryImage)) {
                        $addReference($galleryImage);
                    } elseif (is_array($galleryImage)) {
                        foreach (['path', 'url', 'image', 'image_path', 'src'] as $key) {
                            if (!empty($galleryImage[$key])) {
                                $addReference($galleryImage[$key]);
                            }
                        }
                    }
                }
            }
        }, 'product_id');

        $files = [];
        $totalSize = 0;

        if (is_dir($directory)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $relative = str_replace('\\', '/', $iterator->getSubPathName());
                if (isset($referenced[$relative])) {
                    continue;
                }

                $size = (int) $file->getSize();
                $files[] = [
                    'name' => $relative,
                    'full_path' => $file->getPathname(),
                    'size' => $size,
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
                $totalSize += $size;
            }
        }

        usort($files, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return [
            'files' => $files,
            'totalSize' => $totalSize,
            'referencedCount' => count($referenced),
        ];
    }

    public function productDelete(Product $product)
    {
        // Delete every physical image belonging to the product before deleting
        // the database record. Product images are stored in public/uploads/products.
        $imagePaths = [
            $product->thumbnail,
            $product->featured_image,
        ];

        // Keep this compatible with older products that may have gallery_images
        // stored as a JSON array of paths.
        foreach ((array) ($product->gallery_images ?? []) as $galleryImage) {
            if (is_string($galleryImage)) {
                $imagePaths[] = $galleryImage;
            } elseif (is_array($galleryImage)) {
                foreach (['path', 'url', 'image', 'image_path', 'src'] as $key) {
                    if (!empty($galleryImage[$key]) && is_string($galleryImage[$key])) {
                        $imagePaths[] = $galleryImage[$key];
                        break;
                    }
                }
            }
        }

        foreach (array_unique(array_filter($imagePaths)) as $imagePath) {
            $this->deleteProductImage($imagePath);
        }

        $product->delete();

        return back()->with('success','Product and its images deleted successfully.');
    }

    /**
     * Safely delete a product image from the public uploads directory.
     * Supports both current `uploads/products/file.ext` paths and older records
     * that stored only `file.ext` or `/uploads/products/file.ext`.
     */
    private function deleteProductImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = trim(str_replace('\\', '/', $path));
        $path = ltrim($path, '/');

        // Ignore remote URLs; never attempt to delete arbitrary filesystem paths.
        if (preg_match('#^(https?:)?//#i', $path)) {
            return;
        }

        $relative = $path;
        if (str_starts_with($relative, 'storage/')) {
            $relative = substr($relative, strlen('storage/'));
            $storageFile = storage_path('app/public/' . $relative);
            if (is_file($storageFile)) {
                @unlink($storageFile);
            }
            return;
        }

        if (!str_starts_with($relative, 'uploads/products/')) {
            $relative = 'uploads/products/' . basename($relative);
        }

        $publicRoot = realpath(public_path());
        $target = realpath(public_path($relative));

        // realpath() can return false for a missing file. When it exists, ensure
        // the resolved target remains inside public/ before deleting it.
        if ($target && $publicRoot && str_starts_with($target, $publicRoot . DIRECTORY_SEPARATOR) && is_file($target)) {
            @unlink($target);
        }
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
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'image', 'max:4096'],
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

        // Gallery images are intentionally not limited to five. The database JSON
        // column can hold any number of image paths; the admin form lets the user
        // select as many files as the server upload limits allow. New images are
        // appended when editing an existing product.
        $newGalleryImages = [];
        if ($request->hasFile('gallery_images')) {
            $dir = public_path('uploads/products');
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            foreach ($request->file('gallery_images', []) as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $extension = strtolower($file->getClientOriginalExtension());
                $name = 'gallery_' . Str::random(24) . '.' . $extension;
                $file->move($dir, $name);
                $newGalleryImages[] = 'uploads/products/' . $name;
            }
        }

        if ($product) {
            $existingGallery = is_array($product->gallery_images)
                ? $product->gallery_images
                : (json_decode((string) $product->gallery_images, true) ?: []);

            // Keep existing gallery images unless the admin explicitly marks them
            // for removal in the edit form.
            $removeGallery = array_values(array_filter(
                (array) $request->input('remove_gallery_images', []),
                fn ($value) => is_string($value) && trim($value) !== ''
            ));

            if ($removeGallery) {
                foreach ($removeGallery as $removePath) {
                    $this->deleteProductImage($removePath);
                }
            }

            $existingGallery = array_values(array_filter(
                $existingGallery,
                function ($image) use ($removeGallery) {
                    $path = is_string($image) ? $image : '';
                    if ($path === '') {
                        return false;
                    }
                    return ! in_array($path, $removeGallery, true);
                }
            ));

            $d['gallery_images'] = array_values(array_unique(array_merge($existingGallery, $newGalleryImages)));
        } elseif ($newGalleryImages) {
            $d['gallery_images'] = $newGalleryImages;
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

    public function categoryStore(Request $request){$d=$request->validate(['category_name'=>'required|string|max:255','category_image'=>'nullable|image|max:4096']);if($request->hasFile('category_image')){$dir=public_path('uploads/categories');if(!is_dir($dir))@mkdir($dir,0775,true);$name=uniqid('cat_').'.'.$request->file('category_image')->getClientOriginalExtension();$request->file('category_image')->move($dir,$name);$d['category_image']='uploads/categories/'.$name;}$category=new Category();$category->timestamps=false;$category->fill($d);$category->save();return back()->with('success','Category added successfully.');}
    public function subcategoryStore(Request $request)
{
    $data = $request->validate([
        'category_id' => 'required|integer|exists:categories,category_id',
        'subcategory_name' => 'required|string|max:255',
    ]);

    $subcategory = new Subcategory();
    $subcategory->timestamps = false;
    $subcategory->fill($data);
    $subcategory->save();

    return back()->with('success', 'Subcategory added successfully.');
}
  public function brandStore(Request $request)
{
    $data = $request->validate([
        'subcategory_id' => 'required|integer|exists:subcategories,subcategory_id',
        'brand_name' => 'required|string|max:255',
    ]);

    $brand = new Brand();
    $brand->timestamps = false;
    $brand->fill($data);
    $brand->save();

    return back()->with('success', 'Brand added successfully.');
}
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
        return view('admin.invoice.sale-invoice', compact('order','company'));
    }

    public function tracking(Request $request){$orders=Order::query()->when($request->filled('q'),fn($q)=>$q->where('order_id',$request->integer('q')))->latest('order_id')->paginate(20)->withQueryString();return view('admin.tracking',compact('orders'));}
}
