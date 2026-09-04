@extends('layouts.app')

@section('content')
@php
    $regularPrice = (float) $product->original_price;
    $salePrice = (float) $product->sale_price;
    $discountAmount = (float) $product->discount_amount;
    $hasDiscount = $regularPrice > 0 && $salePrice < $regularPrice;
    $discountPercent = (float) $product->discount_percent_calculated;
    $currencySymbol = (string) \App\Models\Setting::get('general.currency_symbol', '৳');
    $currencyPosition = (string) \App\Models\Setting::get('general.currency_position', 'before');
    $decimals = (int) \App\Models\Setting::get('general.currency_decimals', 2);
    $money = function ($amount) use ($currencySymbol, $currencyPosition, $decimals) {
        $value = number_format((float) $amount, $decimals);
        return $currencyPosition === 'after' ? $value . ' ' . $currencySymbol : $currencySymbol . ' ' . $value;
    };

    $gallery = [];
    if (!empty($product->gallery_images)) {
        $gallery = is_array($product->gallery_images)
            ? $product->gallery_images
            : (json_decode($product->gallery_images, true) ?: []);
    }

    $images = [];
    $addImage = function ($image) use (&$images) {
        // Support current string paths and older associative gallery formats.
        if (is_array($image)) {
            $image = $image['path'] ?? $image['url'] ?? $image['image'] ?? $image['image_path'] ?? $image['src'] ?? null;
        }
        if (!is_string($image) || trim($image) === '') return;
        $image = trim($image);

        // Normalize both old and current database formats.
        // Current uploads are stored as: uploads/products/filename.ext
        // Older records may contain only: filename.ext or /uploads/products/filename.ext
        if (preg_match('/^https?:\/\//i', $image)) {
            $url = $image;
        } else {
            $normalized = ltrim($image, '/');
            if (str_starts_with($normalized, 'uploads/products/')) {
                $url = asset($normalized);
            } elseif (str_starts_with($normalized, 'storage/')) {
                $url = asset($normalized);
            } else {
                $url = asset('uploads/products/' . $normalized);
            }
        }

        if (!in_array($url, $images, true)) $images[] = $url;
    };
    $addImage($product->featured_image);
    $addImage($product->thumbnail);

    // Show every gallery image saved for this product; there is no five-image limit.
    foreach ($gallery as $image) $addImage($image);
    if (!$images) $images[] = asset('images/product-placeholder.png');

    $stockQty = (int) ($product->stock_qty ?? 0);
    $inStock = ($product->stock_status ?? 'Out of Stock') === 'In Stock' && $stockQty > 0;
    $minQty = max(1, (int) ($product->min_order_qty ?? 1));
    $maxQty = $product->max_order_qty ? (int) $product->max_order_qty : max($minQty, $stockQty ?: 999);
    $maxQty = max($minQty, $maxQty);
@endphp

<div class="product-page bg-slate-50/80 text-slate-900">
    {{-- Breadcrumb --}}
    <div class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-3 text-sm sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="font-medium text-slate-500 transition hover:text-indigo-600">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            @if($product->category)
                <span class="max-w-[180px] truncate font-medium text-slate-500">{{ $product->category->category_name }}</span>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
            @endif
            <span class="max-w-[260px] truncate font-semibold text-slate-800">{{ $product->product_name }}</span>
        </div>
    </div>

    {{-- Main product --}}
    <section class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="grid gap-7 lg:grid-cols-[minmax(0,1.08fr)_minmax(420px,.92fr)] lg:gap-10">
            {{-- Gallery --}}
            <div class="min-w-0">
                <div class="lg:sticky lg:top-24">
                    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_70px_-35px_rgba(15,23,42,.35)]">
                        <div id="mainImageStage" class="group relative flex aspect-square min-h-[330px] items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_center,#fff_0,#f8fafc_62%,#eef2f7_100%)] p-5 sm:p-8 lg:p-10" data-zoom="2.15">
                            @if($hasDiscount)
                                <span class="absolute left-5 top-5 z-20 rounded-full bg-rose-600 px-3 py-1.5 text-xs font-black text-white shadow-lg">-{{ $discountPercent }}% OFF</span>
                            @endif
                            @if($product->is_featured)
                                <span class="absolute right-5 top-5 z-20 rounded-full bg-amber-400 px-3 py-1.5 text-xs font-black text-slate-950 shadow-lg"><i class="fa-solid fa-star mr-1"></i> Featured</span>
                            @endif

                            <div class="pointer-events-none absolute inset-0 opacity-60 [background-image:linear-gradient(rgba(148,163,184,.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,.08)_1px,transparent_1px)] [background-size:32px_32px]"></div>
                            <img id="mainProductImage" src="{{ $images[0] }}" alt="{{ $product->product_name }}" class="relative z-10 h-full w-full select-none object-contain transition-transform duration-150 ease-out will-change-transform" draggable="false">
                            <button type="button" onclick="openImageLightbox()" class="absolute bottom-4 right-4 z-20 grid h-11 w-11 place-items-center rounded-full border border-slate-200 bg-white/95 text-slate-700 opacity-0 shadow-lg transition group-hover:opacity-100 hover:bg-slate-950 hover:text-white" aria-label="Open product image">
                                <i class="fa-solid fa-expand"></i>
                            </button>
                            <div class="absolute bottom-4 left-1/2 z-20 hidden -translate-x-1/2 rounded-full bg-slate-950/75 px-3 py-1.5 text-[11px] font-semibold text-white backdrop-blur sm:block">
                                <i class="fa-solid fa-magnifying-glass-plus mr-1"></i> Hover to zoom
                            </div>
                        </div>
                    </div>

                    @if(count($images) > 1)
                        <div class="relative mt-4">
                            <div id="productThumbnails" class="flex gap-3 overflow-x-auto pb-2 [scrollbar-width:thin]">
                                @foreach($images as $index => $image)
                                    <button type="button" data-image="{{ $image }}" onclick="changeProductImage(this.dataset.image, this)" class="product-thumb group relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl border-2 {{ $index === 0 ? 'border-indigo-600 ring-4 ring-indigo-50' : 'border-slate-200' }} bg-white p-1.5 transition hover:border-indigo-400 sm:h-24 sm:w-24" aria-label="View image {{ $index + 1 }}">
                                        <img src="{{ $image }}" alt="{{ $product->product_name }} image {{ $index + 1 }}" loading="lazy" class="h-full w-full rounded-xl object-contain transition duration-300 group-hover:scale-105">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-center">
                            <i class="fa-solid fa-shield-halved text-indigo-600"></i><p class="mt-1 text-[11px] font-bold text-slate-600">Quality checked</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-center">
                            <i class="fa-solid fa-truck-fast text-indigo-600"></i><p class="mt-1 text-[11px] font-bold text-slate-600">Reliable delivery</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-center">
                            <i class="fa-solid fa-headset text-indigo-600"></i><p class="mt-1 text-[11px] font-bold text-slate-600">Support available</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product information --}}
            <div class="min-w-0">
                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_20px_70px_-45px_rgba(15,23,42,.4)] sm:p-7 lg:p-8">
                    @if($product->category)
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-black uppercase tracking-[.18em] text-indigo-700">{{ $product->category->category_name }}</span>
                    @endif
                    <h1 class="mt-3 text-3xl font-black leading-[1.08] tracking-tight text-slate-950 sm:text-4xl lg:text-[42px]">{{ $product->product_name }}</h1>

                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                        @if($product->sku)<span class="text-slate-500">SKU <strong class="text-slate-800">{{ $product->sku }}</strong></span>@endif
                        @if($product->brand)<span class="text-slate-500">Brand <strong class="text-slate-800">{{ $product->brand->brand_name }}</strong></span>@endif
                        @if($product->is_new)<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-black text-emerald-700">NEW</span>@endif
                    </div>

                    <div class="my-6 h-px bg-slate-100"></div>

                    <div class="flex flex-wrap items-end gap-3">
                        <span class="text-4xl font-black tracking-tight text-indigo-700 sm:text-5xl">{{ $money($salePrice) }}</span>
                        @if($hasDiscount)
                            <span class="mb-1 text-lg text-slate-400 line-through">{{ $money($regularPrice) }}</span>
                            <span class="mb-1 rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-black text-rose-600">Save {{ $money($discountAmount) }}</span>
                        @endif
                    </div>

                    @if($product->short_description)
                        <p class="mt-5 whitespace-pre-line text-[15px] leading-7 text-slate-600">{{ $product->short_description }}</p>
                    @endif

                    <div class="mt-5 rounded-2xl {{ $inStock ? 'bg-emerald-50' : 'bg-rose-50' }} p-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl {{ $inStock ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}"><i class="fa-solid {{ $inStock ? 'fa-check' : 'fa-xmark' }}"></i></span>
                            <div><p class="text-sm font-black {{ $inStock ? 'text-emerald-800' : 'text-rose-800' }}">{{ $inStock ? 'In Stock' : 'Out of Stock' }}</p><p class="text-xs {{ $inStock ? 'text-emerald-700' : 'text-rose-700' }}">{{ $inStock ? ($stockQty . ' units currently available') : 'This product is currently unavailable.' }}</p></div>
                        </div>
                    </div>

                    @if($inStock)
                        <form method="POST" action="{{ route('cart.add', $product) }}" id="addToCartForm" class="mt-6">
                            @csrf
                            <div class="grid gap-3 sm:grid-cols-[140px_1fr]">
                                <div>
                                    <label for="productQty" class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">Quantity</label>
                                    <div class="flex h-13 overflow-hidden rounded-2xl border border-slate-300 bg-white">
                                        <button type="button" onclick="decreaseQty()" class="w-11 text-lg font-bold text-slate-500 transition hover:bg-slate-50">−</button>
                                        <input id="productQty" type="number" name="qty" value="{{ $minQty }}" min="{{ $minQty }}" max="{{ $maxQty }}" class="min-w-0 flex-1 border-x border-slate-200 text-center font-black outline-none">
                                        <button type="button" onclick="increaseQty()" class="w-11 text-lg font-bold text-slate-500 transition hover:bg-slate-50">+</button>
                                    </div>
                                </div>
                                <div class="flex items-end gap-2">
                                    <button type="submit" class="flex h-13 flex-1 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 font-black text-white shadow-xl shadow-slate-950/15 transition hover:-translate-y-0.5 hover:bg-indigo-700"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
                                    <button type="button" onclick="buyNow()" class="flex h-13 flex-1 items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 font-black text-white shadow-xl shadow-indigo-600/20 transition hover:-translate-y-0.5 hover:bg-indigo-700"><i class="fa-solid fa-bolt"></i> Buy Now</button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <div class="mt-7 grid gap-2 sm:grid-cols-2">
                        <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3.5"><span class="grid h-9 w-9 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm"><i class="fa-solid fa-lock"></i></span><div><p class="text-xs font-black">Secure ordering</p><p class="text-[11px] text-slate-500">Protected checkout process</p></div></div>
                        <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3.5"><span class="grid h-9 w-9 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm"><i class="fa-solid fa-comments"></i></span><div><p class="text-xs font-black">Need help?</p><p class="text-[11px] text-slate-500">Contact our support team</p></div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="mt-7 grid gap-7 lg:grid-cols-[minmax(0,1.45fr)_minmax(300px,.55fr)]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-file-lines"></i></span><div><p class="text-xs font-black uppercase tracking-widest text-indigo-600">Product details</p><h2 class="text-2xl font-black">Description</h2></div></div>
                <div class="mt-6 whitespace-pre-line break-words text-[15px] leading-8 text-slate-600">{{ $product->long_description ?: 'No detailed description available for this product.' }}</div>
                @if($product->specifications)
                    <div class="mt-8 border-t border-slate-100 pt-7"><h3 class="text-xl font-black">Specifications</h3><div class="mt-4 whitespace-pre-line break-words text-[15px] leading-8 text-slate-600">{{ $product->specifications }}</div></div>
                @endif
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-xl font-black">Product information</h2>
                <div class="mt-4 divide-y divide-slate-100">
                    @foreach([
                        'SKU' => $product->sku,
                        'Brand' => $product->brand?->brand_name,
                        'Category' => $product->category?->category_name,
                        'Weight' => $product->weight,
                        'Dimensions' => $product->dimensions,
                    ] as $label => $value)
                        @if($value !== null && $value !== '')<div class="flex justify-between gap-4 py-3 text-sm"><span class="text-slate-500">{{ $label }}</span><span class="text-right font-bold text-slate-900">{{ $value }}</span></div>@endif
                    @endforeach
                    @if($product->shipping_cost !== null)<div class="flex justify-between gap-4 py-3 text-sm"><span class="text-slate-500">Shipping</span><span class="font-bold text-slate-900">{{ $money($product->shipping_cost) }}</span></div>@endif
                    <div class="flex justify-between gap-4 py-3 text-sm"><span class="text-slate-500">Availability</span><span class="font-bold {{ $inStock ? 'text-emerald-600' : 'text-rose-600' }}">{{ $inStock ? 'In Stock' : 'Out of Stock' }}</span></div>
                </div>
            </div>
        </div>

        {{-- Related products --}}
        @if($relatedProducts->count())
            <section class="mt-12 sm:mt-16">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div><p class="text-xs font-black uppercase tracking-[.2em] text-indigo-600">More to explore</p><h2 class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Related Products</h2><p class="mt-1 text-sm text-slate-500">Products selected from the same category and brand.</p></div>
                    <a href="{{ route('shop') }}" class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 sm:inline-flex">View all <i class="fa-solid fa-arrow-right ml-2"></i></a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
                    @foreach($relatedProducts as $related)
                        @php $rRegular=(float)$related->original_price; $rPrice=(float)$related->sale_price; $rDiscount=(float)$related->discount_percent_calculated; @endphp
                        <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-[0_20px_45px_-25px_rgba(79,70,229,.45)]">
                            <a href="{{ route('product.show', ['slug' => $related->slug ?: $related->product_id]) }}" class="block">
                                <div class="relative aspect-square overflow-hidden bg-slate-50">
                                    @if($rDiscount > 0)<span class="absolute left-3 top-3 z-10 rounded-full bg-rose-600 px-2 py-1 text-[10px] font-black text-white">-{{ $rDiscount }}%</span>@endif
                                    @if($related->thumbnail || $related->featured_image)
                                        <img src="{{ preg_match('/^https?:\/\//i', (string)($related->thumbnail ?: $related->featured_image)) ? ($related->thumbnail ?: $related->featured_image) : asset(str_starts_with(ltrim((string)($related->thumbnail ?: $related->featured_image), '/'), 'uploads/products/') ? ltrim((string)($related->thumbnail ?: $related->featured_image), '/') : 'uploads/products/' . ltrim((string)($related->thumbnail ?: $related->featured_image), '/')) }}" alt="{{ $related->product_name }}" loading="lazy" class="h-full w-full object-contain p-5 transition duration-500 group-hover:scale-110">
                                    @else
                                        <div class="grid h-full place-items-center text-slate-300"><i class="fa-regular fa-image text-4xl"></i></div>
                                    @endif
                                    <div class="absolute inset-x-3 bottom-3 translate-y-2 rounded-xl bg-slate-950/90 px-3 py-2 text-center text-xs font-black text-white opacity-0 transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">View product</div>
                                </div>
                                <div class="p-4 sm:p-5">
                                    @if($related->brand)<p class="text-[10px] font-black uppercase tracking-wider text-indigo-600">{{ $related->brand->brand_name }}</p>@endif
                                    <h3 class="mt-1 line-clamp-2 min-h-[42px] text-sm font-black leading-5 text-slate-800 transition group-hover:text-indigo-600 sm:text-[15px]">{{ $related->product_name }}</h3>
                                    <div class="mt-3 flex flex-wrap items-center gap-2"><span class="font-black text-slate-950">{{ $money($rPrice) }}</span>@if($rRegular>$rPrice)<span class="text-xs text-slate-400 line-through">{{ $money($rRegular) }}</span>@endif</div>
                                    <p class="mt-2 text-[11px] font-bold {{ (($related->stock_qty ?? 0) > 0) ? 'text-emerald-600' : 'text-rose-500' }}"><i class="fa-solid fa-circle text-[6px] align-middle"></i> {{ (($related->stock_qty ?? 0) > 0) ? 'In Stock' : 'Out of Stock' }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </section>
</div>

{{-- Image lightbox --}}
<div id="imageLightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Product image viewer">
    <button type="button" onclick="closeImageLightbox()" class="absolute right-4 top-4 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <img id="lightboxImage" src="" alt="{{ $product->product_name }}" class="max-h-[92vh] max-w-[94vw] object-contain">
</div>

@push('scripts')
<script>
(function () {
    const stage = document.getElementById('mainImageStage');
    const image = document.getElementById('mainProductImage');
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const zoomFactor = stage ? parseFloat(stage.dataset.zoom || '2') : 2;

    window.changeProductImage = function (url, button) {
        if (!image) return;
        image.src = url;
        image.style.transform = 'scale(1)';
        image.style.transformOrigin = 'center center';
        document.querySelectorAll('.product-thumb').forEach(function (thumb) {
            thumb.classList.remove('border-indigo-600', 'ring-4', 'ring-indigo-50');
            thumb.classList.add('border-slate-200');
        });
        if (button) {
            button.classList.remove('border-slate-200');
            button.classList.add('border-indigo-600', 'ring-4', 'ring-indigo-50');
        }
    };

    if (stage && image) {
        stage.addEventListener('mousemove', function (event) {
            if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
                const rect = stage.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;
                image.style.transformOrigin = x + '% ' + y + '%';
                image.style.transform = 'scale(' + zoomFactor + ')';
            }
        });
        stage.addEventListener('mouseleave', function () {
            image.style.transform = 'scale(1)';
            image.style.transformOrigin = 'center center';
        });
    }

    window.openImageLightbox = function () {
        if (!lightbox || !lightboxImage || !image) return;
        lightboxImage.src = image.src;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };
    window.closeImageLightbox = function () {
        if (!lightbox) return;
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') window.closeImageLightbox();
    });

    window.increaseQty = function () {
        const input = document.getElementById('productQty'); if (!input) return;
        const max = parseInt(input.max || '999', 10), current = parseInt(input.value || '1', 10);
        input.value = Math.min(max, current + 1);
    };
    window.decreaseQty = function () {
        const input = document.getElementById('productQty'); if (!input) return;
        const min = parseInt(input.min || '1', 10), current = parseInt(input.value || '1', 10);
        input.value = Math.max(min, current - 1);
    };
    window.buyNow = function () {
        const form = document.getElementById('addToCartForm'); if (!form) return;
        let field = form.querySelector('input[name="buy_now"]');
        if (!field) { field = document.createElement('input'); field.type = 'hidden'; field.name = 'buy_now'; form.appendChild(field); }
        field.value = '1'; form.submit();
    };
})();
</script>
@endpush
@endsection
