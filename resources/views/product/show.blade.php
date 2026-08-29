@extends('layouts.app')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Product Price
    |--------------------------------------------------------------------------
    */

    $regularPrice = (float) ($product->price ?? 0);

    $discountPrice = $product->discount_price !== null
        ? (float) $product->discount_price
        : $regularPrice;

    $hasDiscount = $regularPrice > 0
        && $discountPrice > 0
        && $discountPrice < $regularPrice;

    $discountPercent = $product->discount_percent;

    if (!$discountPercent && $hasDiscount) {
        $discountPercent = round(
            (($regularPrice - $discountPrice) / $regularPrice) * 100
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Gallery
    |--------------------------------------------------------------------------
    */

    $gallery = [];

    if (!empty($product->gallery_images)) {
        if (is_array($product->gallery_images)) {
            $gallery = $product->gallery_images;
        } else {
            $decodedGallery = json_decode($product->gallery_images, true);

            if (is_array($decodedGallery)) {
                $gallery = $decodedGallery;
            }
        }
    }

    $images = [];

    if (!empty($product->thumbnail)) {
        $images[] = asset('uploads/products/' . $product->thumbnail);
    }

    if (!empty($product->featured_image)) {
        $featured = asset('uploads/products/' . $product->featured_image);

        if (!in_array($featured, $images)) {
            $images[] = $featured;
        }
    }

    foreach ($gallery as $image) {
        if (is_string($image) && $image !== '') {
            $imageUrl = str_starts_with($image, 'http')
                ? $image
                : asset('uploads/products/' . $image);

            if (!in_array($imageUrl, $images)) {
                $images[] = $imageUrl;
            }
        }
    }

    if (empty($images)) {
        $images[] = asset('images/product-placeholder.png');
    }

    /*
    |--------------------------------------------------------------------------
    | Stock
    |--------------------------------------------------------------------------
    */

    $stockQty = (int) ($product->stock_qty ?? 0);

    $inStock =
        ($product->stock_status ?? 'Out of Stock') === 'In Stock'
        && $stockQty > 0;

    $minQty = max(1, (int) ($product->min_order_qty ?? 1));

    $maxQty = $product->max_order_qty
        ? (int) $product->max_order_qty
        : ($stockQty > 0 ? $stockQty : 999);

    $maxQty = max($minQty, $maxQty);
@endphp


<div class="bg-slate-50">

    {{-- ============================================================
         BREADCRUMB
    ============================================================= --}}

    <div class="border-b bg-white">
        <div class="mx-auto w-full max-w-7xl px-4 py-4 sm:px-6 lg:px-8">

            <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500">

                <a href="{{ url('/') }}"
                   class="transition hover:text-blue-600">
                    Home
                </a>

                <span>/</span>

                @if($product->category)
                    <a href="#"
                       class="transition hover:text-blue-600">
                        {{ $product->category->category_name }}
                    </a>

                    <span>/</span>
                @endif

                <span class="max-w-[250px] truncate font-medium text-slate-800">
                    {{ $product->product_name }}
                </span>

            </nav>

        </div>
    </div>


    {{-- ============================================================
         PRODUCT MAIN SECTION
    ============================================================= --}}

    <section class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">

        <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">


            {{-- ====================================================
                 PRODUCT IMAGES
            ===================================================== --}}

            <div>

                <div class="sticky top-6">

                    {{-- Main Image --}}

                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        @if($hasDiscount)
                            <div class="absolute left-4 top-4 z-10">
                                <span class="rounded-full bg-red-600 px-4 py-2 text-sm font-bold text-white shadow">
                                    -{{ $discountPercent }}% OFF
                                </span>
                            </div>
                        @endif

                        @if($product->is_featured)
                            <div class="absolute right-4 top-4 z-10">
                                <span class="rounded-full bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow">
                                    Featured
                                </span>
                            </div>
                        @endif

                        <div class="flex aspect-square items-center justify-center p-5 sm:p-8">

                            <img
                                id="mainProductImage"
                                src="{{ $images[0] }}"
                                alt="{{ $product->product_name }}"
                                class="h-full w-full object-contain transition duration-300"
                            >

                        </div>

                    </div>


                    {{-- Thumbnail Gallery --}}

                    @if(count($images) > 1)

                        <div class="mt-4 grid grid-cols-5 gap-3 sm:grid-cols-6">

                            @foreach($images as $index => $image)

                                <button
                                    type="button"
                                    onclick="changeProductImage('{{ $image }}', this)"
                                    class="product-thumb aspect-square overflow-hidden rounded-xl border-2 {{ $index === 0 ? 'border-blue-600' : 'border-slate-200' }} bg-white p-1 transition hover:border-blue-500"
                                >

                                    <img
                                        src="{{ $image }}"
                                        alt="{{ $product->product_name }} image {{ $index + 1 }}"
                                        class="h-full w-full rounded-lg object-contain"
                                    >

                                </button>

                            @endforeach

                        </div>

                    @endif

                </div>

            </div>


            {{-- ====================================================
                 PRODUCT INFORMATION
            ===================================================== --}}

            <div class="min-w-0">


                {{-- Category --}}

                @if($product->category)
                    <a href="#"
                       class="text-sm font-semibold uppercase tracking-wide text-blue-600 hover:text-blue-700">
                        {{ $product->category->category_name }}
                    </a>
                @endif


                {{-- Product Name --}}

                <h1 class="mt-2 break-words text-3xl font-black leading-tight tracking-tight text-slate-950 sm:text-4xl">
                    {{ $product->product_name }}
                </h1>


                {{-- SKU / Brand --}}

                <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">

                    @if($product->sku)
                        <span>
                            SKU:
                            <strong class="text-slate-700">
                                {{ $product->sku }}
                            </strong>
                        </span>
                    @endif

                    @if($product->brand)
                        <span>
                            Brand:
                            <strong class="text-slate-700">
                                {{ $product->brand->brand_name }}
                            </strong>
                        </span>
                    @endif

                </div>


                {{-- Price --}}

                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">

                    <div class="flex flex-wrap items-end gap-3">

                        <span class="text-3xl font-black text-blue-700 sm:text-4xl">
                            ৳ {{ number_format($discountPrice, 2) }}
                        </span>

                        @if($hasDiscount)

                            <span class="mb-1 text-lg text-slate-400 line-through">
                                ৳ {{ number_format($regularPrice, 2) }}
                            </span>

                            <span class="mb-1 rounded-lg bg-red-50 px-2.5 py-1 text-sm font-bold text-red-600">
                                Save ৳ {{ number_format($regularPrice - $discountPrice, 2) }}
                            </span>

                        @endif

                    </div>

                    <p class="mt-2 text-xs text-slate-500">
                        Price includes applicable product pricing. Shipping charges may apply.
                    </p>

                </div>


                {{-- Stock --}}

                <div class="mt-5">

                    @if($inStock)

                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">

                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>

                            In Stock

                            @if($stockQty > 0)
                                <span class="font-normal">
                                    ({{ $stockQty }} available)
                                </span>
                            @endif

                        </div>

                    @else

                        <div class="inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-bold text-red-600">

                            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>

                            Out of Stock

                        </div>

                    @endif

                </div>


                {{-- Short Description --}}

                @if($product->short_description)

                    <div class="mt-6">

                        <p class="whitespace-pre-line text-base leading-7 text-slate-600">
                            {{ $product->short_description }}
                        </p>

                    </div>

                @endif


                {{-- Purchase Box --}}

                @if($inStock)

                    <div class="mt-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <form
                            method="POST"
                            action="{{ route('cart.add', $product) }}"
                            id="addToCartForm"
                        >

                            @csrf

                            <div class="flex flex-col gap-4 sm:flex-row">

                                {{-- Quantity --}}

                                <div>

                                    <label class="mb-2 block text-sm font-bold text-slate-700">
                                        Quantity
                                    </label>

                                    <div class="flex h-12 overflow-hidden rounded-xl border border-slate-300">

                                        <button
                                            type="button"
                                            onclick="decreaseQty()"
                                            class="w-12 bg-slate-50 text-xl font-bold text-slate-700 hover:bg-slate-100"
                                        >
                                            −
                                        </button>

                                        <input
                                            id="productQty"
                                            type="number"
                                            name="qty"
                                            value="{{ $minQty }}"
                                            min="{{ $minQty }}"
                                            max="{{ $maxQty }}"
                                            class="w-16 border-x border-slate-300 text-center font-bold outline-none"
                                        >

                                        <button
                                            type="button"
                                            onclick="increaseQty()"
                                            class="w-12 bg-slate-50 text-xl font-bold text-slate-700 hover:bg-slate-100"
                                        >
                                            +
                                        </button>

                                    </div>

                                </div>


                                {{-- Buttons --}}

                                <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-end">

                                    <button
                                        type="submit"
                                        class="flex h-12 flex-1 items-center justify-center rounded-xl bg-blue-700 px-6 font-bold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 active:scale-[0.99]"
                                    >
                                        🛒 Add to Cart
                                    </button>

                                    <button
                                        type="button"
                                        onclick="buyNow()"
                                        class="flex h-12 flex-1 items-center justify-center rounded-xl border-2 border-blue-700 px-6 font-bold text-blue-700 transition hover:bg-blue-50"
                                    >
                                        Buy Now
                                    </button>

                                </div>

                            </div>

                        </form>

                        @if($minQty > 1 || $product->max_order_qty)
                            <div class="mt-3 text-xs text-slate-500">
                                Order quantity:
                                {{ $minQty }}
                                @if($product->max_order_qty)
                                    – {{ $product->max_order_qty }}
                                @endif
                            </div>
                        @endif

                    </div>

                @endif


                {{-- Delivery / Service Information --}}

                <div class="mt-6 grid gap-3 sm:grid-cols-2">

                    <div class="rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex gap-3">

                            <div class="text-xl">
                                🚚
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900">
                                    Delivery
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Fast and reliable delivery service.
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex gap-3">

                            <div class="text-xl">
                                🔒
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900">
                                    Secure Order
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Your order information is protected.
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex gap-3">

                            <div class="text-xl">
                                📦
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900">
                                    Easy Ordering
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Simple and convenient checkout.
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="rounded-2xl border border-slate-200 bg-white p-4">

                        <div class="flex gap-3">

                            <div class="text-xl">
                                💬
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900">
                                    Customer Support
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Contact us for product assistance.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


            </div>

        </div>


        {{-- ============================================================
             PRODUCT DETAILS
        ============================================================= --}}

        <div class="mt-12 grid gap-8 lg:grid-cols-3">


            {{-- Description --}}

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2 sm:p-8">

                <h2 class="text-2xl font-black text-slate-950">
                    Product Description
                </h2>

                <div class="mt-5 whitespace-pre-line break-words leading-8 text-slate-600">

                    @if($product->long_description)
                        {{ $product->long_description }}
                    @else
                        <p class="text-slate-400">
                            No detailed description available for this product.
                        </p>
                    @endif

                </div>

            </div>


            {{-- Product Information --}}

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <h2 class="text-xl font-black text-slate-950">
                    Product Information
                </h2>

                <div class="mt-5 divide-y divide-slate-100">

                    @if($product->sku)
                        <div class="flex justify-between gap-4 py-3 text-sm">
                            <span class="text-slate-500">SKU</span>
                            <span class="text-right font-semibold text-slate-900">
                                {{ $product->sku }}
                            </span>
                        </div>
                    @endif

                    @if($product->brand)
                        <div class="flex justify-between gap-4 py-3 text-sm">
                            <span class="text-slate-500">Brand</span>
                            <span class="text-right font-semibold text-slate-900">
                                {{ $product->brand->brand_name }}
                            </span>
                        </div>
                    @endif

                    @if($product->weight)
                        <div class="flex justify-between gap-4 py-3 text-sm">
                            <span class="text-slate-500">Weight</span>
                            <span class="text-right font-semibold text-slate-900">
                                {{ $product->weight }}
                            </span>
                        </div>
                    @endif

                    @if($product->dimensions)
                        <div class="flex justify-between gap-4 py-3 text-sm">
                            <span class="text-slate-500">Dimensions</span>
                            <span class="text-right font-semibold text-slate-900">
                                {{ $product->dimensions }}
                            </span>
                        </div>
                    @endif

                    @if($product->shipping_cost !== null)
                        <div class="flex justify-between gap-4 py-3 text-sm">
                            <span class="text-slate-500">Shipping</span>
                            <span class="text-right font-semibold text-slate-900">
                                ৳ {{ number_format($product->shipping_cost, 2) }}
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between gap-4 py-3 text-sm">
                        <span class="text-slate-500">Availability</span>

                        <span class="font-semibold {{ $inStock ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $inStock ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
             SPECIFICATIONS
        ============================================================= --}}

        @if($product->specifications)

            <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <h2 class="text-2xl font-black text-slate-950">
                    Specifications
                </h2>

                <div class="mt-5 whitespace-pre-line break-words leading-8 text-slate-600">
                    {{ $product->specifications }}
                </div>

            </div>

        @endif


        {{-- ============================================================
             RELATED PRODUCTS
        ============================================================= --}}

        @if(isset($relatedProducts) && $relatedProducts->count())

            <section class="mt-14">

                <div class="mb-6 flex items-end justify-between gap-4">

                    <div>

                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                            You may also like
                        </p>

                        <h2 class="mt-1 text-2xl font-black text-slate-950 sm:text-3xl">
                            Related Products
                        </h2>

                    </div>

                </div>


                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">

                    @foreach($relatedProducts as $related)

                        @php
                            $relatedRegular = (float) ($related->price ?? 0);

                            $relatedPrice = $related->discount_price !== null
                                ? (float) $related->discount_price
                                : $relatedRegular;

                            $relatedDiscount =
                                $related->discount_percent
                                ?: (
                                    $relatedRegular > 0 &&
                                    $relatedPrice < $relatedRegular
                                    ? round((($relatedRegular - $relatedPrice) / $relatedRegular) * 100)
                                    : 0
                                );
                        @endphp

                        <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                            <a href="{{ route('products.show', $related) }}"
                               class="block">

                                <div class="relative aspect-square overflow-hidden bg-slate-50">

                                    @if($related->discount_price !== null && $relatedDiscount > 0)

                                        <span class="absolute left-2 top-2 z-10 rounded-full bg-red-600 px-2.5 py-1 text-[11px] font-bold text-white">
                                            -{{ $relatedDiscount }}%
                                        </span>

                                    @endif

                                    @if($related->thumbnail)

                                        <img
                                            src="{{ asset('uploads/products/'.$related->thumbnail) }}"
                                            alt="{{ $related->product_name }}"
                                            loading="lazy"
                                            class="h-full w-full object-contain p-4 transition duration-500 group-hover:scale-105"
                                        >

                                    @else

                                        <div class="flex h-full items-center justify-center text-sm text-slate-400">
                                            No Image
                                        </div>

                                    @endif

                                </div>


                                <div class="p-4">

                                    @if($related->brand)
                                        <p class="text-xs font-medium text-slate-400">
                                            {{ $related->brand->brand_name }}
                                        </p>
                                    @endif

                                    <h3 class="mt-1 line-clamp-2 min-h-[40px] text-sm font-bold text-slate-900 transition group-hover:text-blue-700">
                                        {{ $related->product_name }}
                                    </h3>


                                    <div class="mt-3 flex flex-wrap items-center gap-2">

                                        <span class="font-black text-blue-700">
                                            ৳ {{ number_format($relatedPrice, 2) }}
                                        </span>

                                        @if($relatedRegular > 0 && $relatedPrice < $relatedRegular)

                                            <span class="text-xs text-slate-400 line-through">
                                                ৳ {{ number_format($relatedRegular, 2) }}
                                            </span>

                                        @endif

                                    </div>


                                    @if(($related->stock_status ?? '') === 'In Stock')

                                        <p class="mt-2 text-xs font-semibold text-emerald-600">
                                            ● In Stock
                                        </p>

                                    @else

                                        <p class="mt-2 text-xs font-semibold text-red-500">
                                            ● Out of Stock
                                        </p>

                                    @endif

                                </div>

                            </a>

                        </article>

                    @endforeach

                </div>

            </section>

        @endif


    </section>

</div>


{{-- ================================================================
     JAVASCRIPT
================================================================ --}}

<script>

function changeProductImage(url, button) {

    const mainImage = document.getElementById('mainProductImage');

    if (mainImage) {
        mainImage.src = url;
    }

    document.querySelectorAll('.product-thumb').forEach(function (thumb) {
        thumb.classList.remove('border-blue-600');
        thumb.classList.add('border-slate-200');
    });

    button.classList.remove('border-slate-200');
    button.classList.add('border-blue-600');
}


function increaseQty() {

    const input = document.getElementById('productQty');

    if (!input) return;

    const max = parseInt(input.max || 999, 10);
    const current = parseInt(input.value || 1, 10);

    if (current < max) {
        input.value = current + 1;
    }
}


function decreaseQty() {

    const input = document.getElementById('productQty');

    if (!input) return;

    const min = parseInt(input.min || 1, 10);
    const current = parseInt(input.value || 1, 10);

    if (current > min) {
        input.value = current - 1;
    }
}


function buyNow() {

    const form = document.getElementById('addToCartForm');

    if (!form) return;

    const input = document.createElement('input');

    input.type = 'hidden';
    input.name = 'buy_now';
    input.value = '1';

    form.appendChild(input);

    form.submit();
}

</script>

@endsection