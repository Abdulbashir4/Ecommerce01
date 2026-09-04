@extends('layouts.app')

@section('content')

@php
    $cartCount = collect($cart)->sum(function ($item) {
        return (int) ($item['qty'] ?? 0);
    });

    $subtotal = 0;
    $regularTotal = 0;
    $totalSavings = 0;

    foreach ($cart as $item) {

        $salePrice = (float) ($item['price'] ?? 0);

        $originalPrice = (float) (
            $item['regular_price']
            ?? $salePrice
        );

        if ($originalPrice < $salePrice) {
            $originalPrice = $salePrice;
        }

        $qty = (int) ($item['qty'] ?? 0);

        $subtotal += $salePrice * $qty;

        $regularTotal += $originalPrice * $qty;

        $totalSavings +=
            max(0, $originalPrice - $salePrice) * $qty;
    }
@endphp


<div class="min-h-screen bg-slate-50">


    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="border-b border-slate-200 bg-white">

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <p class="text-sm font-black uppercase tracking-widest text-indigo-600">
                        Shopping Cart
                    </p>

                    <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        Your Cart
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Review your products before proceeding to checkout.
                    </p>

                </div>


                <div
                    id="cart-count-badge"
                    class="w-fit rounded-full bg-indigo-50 px-4 py-2 text-sm font-black text-indigo-700"
                >
                    {{ $cartCount }}
                    {{ $cartCount == 1 ? 'Item' : 'Items' }}
                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
         MAIN
    ========================================================== --}}
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @if(count($cart))


            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">


                {{-- =================================================
                     CART ITEMS
                ================================================== --}}
                <div>

                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">


                        {{-- Header --}}
                        <div class="border-b border-slate-100 px-5 py-5 sm:px-7">

                            <div class="flex items-center justify-between">

                                <div>

                                    <h2 class="text-lg font-black text-slate-950">
                                        Cart Items
                                    </h2>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Quantity changes are automatically saved.
                                    </p>

                                </div>


                                <div class="hidden rounded-full bg-emerald-50 px-3 py-2 text-xs font-black text-emerald-700 sm:block">

                                    <i class="fa-solid fa-tag mr-1"></i>

                                    Sale Price Applied

                                </div>

                            </div>

                        </div>



                        {{-- Items --}}
                        <div id="cart-items">

                            @foreach($cart as $id => $item)

                                @php

                                    $salePrice = (float) (
                                        $item['price'] ?? 0
                                    );

                                    $originalPrice = (float) (
                                        $item['regular_price']
                                        ?? $salePrice
                                    );

                                    if ($originalPrice < $salePrice) {
                                        $originalPrice = $salePrice;
                                    }

                                    $qty = (int) (
                                        $item['qty'] ?? 0
                                    );

                                    $itemTotal =
                                        $salePrice * $qty;

                                    $itemSaving =
                                        max(
                                            0,
                                            ($originalPrice - $salePrice) * $qty
                                        );

                                    $discountPercent =
                                        $originalPrice > 0
                                            ? round(
                                                (
                                                    ($originalPrice - $salePrice)
                                                    / $originalPrice
                                                ) * 100
                                            )
                                            : 0;

                                    $image = $item['image'] ?? null;

                                @endphp



                                <div
                                    data-cart-item
                                    data-id="{{ $id }}"
                                    data-price="{{ $salePrice }}"
                                    data-regular-price="{{ $originalPrice }}"
                                    class="border-b border-slate-100 p-5 transition hover:bg-slate-50 sm:p-6"
                                >

                                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">


                                        {{-- =================================
                                             IMAGE
                                        ================================== --}}
                                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-white sm:h-28 sm:w-28">

                                            @if($image)

                                                @php

                                                    $imagePath = ltrim(
                                                        str_replace(
                                                            '\\',
                                                            '/',
                                                            $image
                                                        ),
                                                        '/'
                                                    );

                                                    if (
                                                        str_starts_with(
                                                            $imagePath,
                                                            'uploads/'
                                                        )
                                                        ||
                                                        str_starts_with(
                                                            $imagePath,
                                                            'storage/'
                                                        )
                                                    ) {

                                                        $imageUrl =
                                                            asset(
                                                                $imagePath
                                                            );

                                                    } else {

                                                        $imageUrl =
                                                            asset(
                                                                'uploads/products/'
                                                                . basename($imagePath)
                                                            );
                                                    }

                                                @endphp


                                                <img
                                                    src="{{ $imageUrl }}"
                                                    alt="{{ $item['name'] }}"
                                                    class="h-full w-full object-contain p-2"
                                                    loading="lazy"
                                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                                >


                                                <div class="hidden h-full w-full items-center justify-center text-slate-300">

                                                    <i class="fa-solid fa-image text-3xl"></i>

                                                </div>

                                            @else

                                                <div class="flex h-full w-full items-center justify-center text-slate-300">

                                                    <i class="fa-solid fa-image text-3xl"></i>

                                                </div>

                                            @endif

                                        </div>



                                        {{-- =================================
                                             PRODUCT INFO
                                        ================================== --}}
                                        <div class="min-w-0 flex-1">

                                            <h3 class="break-words text-base font-black text-slate-950 sm:text-lg">
                                                {{ $item['name'] }}
                                            </h3>


                                            {{-- Sale + Original Price --}}
                                            <div class="mt-2 flex flex-wrap items-center gap-2">


                                                {{-- SALE PRICE --}}
                                                <span
                                                    class="text-xl font-black text-indigo-600"
                                                >
                                                    ৳ {{ number_format($salePrice, 2) }}
                                                </span>


                                                {{-- ORIGINAL PRICE --}}
                                                @if($originalPrice > $salePrice)

                                                    <span class="text-sm font-bold text-slate-400 line-through">
                                                        ৳ {{ number_format($originalPrice, 2) }}
                                                    </span>


                                                    {{-- DISCOUNT --}}
                                                    @if($discountPercent > 0)

                                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-black text-red-600">
                                                            -{{ $discountPercent }}%
                                                        </span>

                                                    @endif

                                                @endif

                                            </div>


                                            {{-- Saving --}}
                                            @if($itemSaving > 0)

                                                <p
                                                    class="mt-1 text-xs font-bold text-emerald-600"
                                                >
                                                    <i class="fa-solid fa-tag mr-1"></i>

                                                    You save ৳
                                                    {{ number_format($itemSaving, 2) }}

                                                </p>

                                            @endif


                                            <p class="mt-1 text-[11px] text-slate-400">
                                                Sale Price / unit
                                            </p>

                                        </div>



                                        {{-- =================================
                                             QUANTITY
                                        ================================== --}}
                                        <div class="flex items-center justify-between gap-5 sm:justify-end">


                                            <div>

                                                <label class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-400">
                                                    Quantity
                                                </label>


                                                <div class="flex h-11 overflow-hidden rounded-xl border border-slate-200 bg-white">

                                                    <button
                                                        type="button"
                                                        data-qty-minus
                                                        class="grid w-10 place-items-center text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                                    >
                                                        <i class="fa-solid fa-minus text-xs"></i>
                                                    </button>


                                                    <input
                                                        type="number"
                                                        min="0"
                                                        value="{{ $qty }}"
                                                        data-qty
                                                        class="w-14 border-x border-slate-200 text-center text-sm font-black outline-none"
                                                    >


                                                    <button
                                                        type="button"
                                                        data-qty-plus
                                                        class="grid w-10 place-items-center text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                                    >
                                                        <i class="fa-solid fa-plus text-xs"></i>
                                                    </button>

                                                </div>

                                            </div>



                                            {{-- ITEM TOTAL --}}
                                            <div class="min-w-[125px] text-right">

                                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                                    Subtotal
                                                </p>

                                                <p
                                                    data-item-total
                                                    class="mt-1 text-base font-black text-slate-950"
                                                >
                                                    ৳ {{ number_format($itemTotal, 2) }}
                                                </p>

                                            </div>


                                        </div>



                                        {{-- =================================
                                             REMOVE
                                        ================================== --}}
                                        <button
                                            type="button"
                                            data-remove
                                            class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-red-100 bg-red-50 px-4 text-sm font-bold text-red-600 transition hover:bg-red-100"
                                        >

                                            <i class="fa-solid fa-trash-can text-xs"></i>

                                            <span>
                                                Remove
                                            </span>

                                        </button>


                                    </div>

                                </div>

                            @endforeach

                        </div>



                        {{-- Continue Shopping --}}
                        <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-7">

                            <a
                                href="/shop"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm transition hover:bg-indigo-50 hover:text-indigo-700"
                            >

                                <i class="fa-solid fa-arrow-left"></i>

                                Continue Shopping

                            </a>

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     ORDER SUMMARY
                ================================================== --}}
                <aside class="lg:sticky lg:top-28 lg:self-start">


                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">


                        {{-- Summary Header --}}
                        <div class="border-b border-slate-100 px-6 py-5">

                            <h2 class="text-lg font-black text-slate-950">
                                Order Summary
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Your current cart total
                            </p>

                        </div>



                        <div class="space-y-5 p-6">


                            {{-- Items --}}
                            <div class="flex justify-between text-sm">

                                <span class="font-semibold text-slate-500">
                                    Items
                                </span>

                                <span
                                    id="summary-count"
                                    class="font-black text-slate-950"
                                >
                                    {{ $cartCount }}
                                </span>

                            </div>



                            {{-- Regular Total --}}
                            @if($regularTotal > $subtotal)

                                <div class="flex justify-between text-sm">

                                    <span class="font-semibold text-slate-500">
                                        Regular Total
                                    </span>

                                    <span
                                        id="regular-total"
                                        class="font-bold text-slate-400 line-through"
                                    >
                                        ৳ {{ number_format($regularTotal, 2) }}
                                    </span>

                                </div>


                                {{-- Savings --}}
                                <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-4 py-3">

                                    <span class="text-sm font-bold text-emerald-700">

                                        <i class="fa-solid fa-tag mr-1"></i>

                                        You Save

                                    </span>

                                    <span
                                        id="cart-savings"
                                        class="text-sm font-black text-emerald-700"
                                    >
                                        ৳ {{ number_format($totalSavings, 2) }}
                                    </span>

                                </div>

                            @endif



                            {{-- Sale Subtotal --}}
                            <div class="flex justify-between text-sm">

                                <span class="font-semibold text-slate-500">
                                    Sale Subtotal
                                </span>

                                <span
                                    id="cart-subtotal"
                                    class="font-black text-slate-950"
                                >
                                    ৳ {{ number_format($subtotal, 2) }}
                                </span>

                            </div>



                            {{-- Delivery --}}
                            <div class="flex justify-between text-sm">

                                <span class="font-semibold text-slate-500">
                                    Delivery
                                </span>

                                <span class="text-xs font-bold text-slate-400">
                                    Calculated at checkout
                                </span>

                            </div>



                            {{-- Final Total --}}
                            <div class="border-t border-dashed border-slate-200 pt-5">

                                <div class="flex items-end justify-between">

                                    <div>

                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            Estimated Total
                                        </p>

                                        <p
                                            id="estimated-total"
                                            class="mt-1 text-2xl font-black text-slate-950"
                                        >
                                            ৳ {{ number_format($subtotal, 2) }}
                                        </p>

                                    </div>


                                    <div class="grid h-10 w-10 place-items-center rounded-full bg-emerald-50 text-emerald-600">

                                        <i class="fa-solid fa-check"></i>

                                    </div>

                                </div>

                            </div>



                            {{-- Checkout --}}
                            <a
                                href="/checkout"
                                class="flex w-full items-center justify-between rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-4 text-sm font-black text-white shadow-lg shadow-indigo-100 transition hover:-translate-y-0.5 hover:from-indigo-700 hover:to-blue-700"
                            >

                                <span class="flex items-center gap-3">

                                    <i class="fa-solid fa-lock"></i>

                                    Proceed to Checkout

                                </span>

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>



                            {{-- Secure --}}
                            <div class="flex gap-3 rounded-2xl bg-slate-50 p-4">

                                <div class="text-emerald-600">

                                    <i class="fa-solid fa-shield-halved"></i>

                                </div>

                                <div>

                                    <p class="text-xs font-black text-slate-700">
                                        Secure Checkout
                                    </p>

                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                        Your order information is handled securely.
                                    </p>

                                </div>

                            </div>


                        </div>

                    </div>

                </aside>


            </div>


        @else


            {{-- =================================================
                 EMPTY CART
            ================================================== --}}
            <div class="mx-auto max-w-2xl py-12">

                <div class="rounded-3xl border border-slate-200 bg-white px-6 py-14 text-center shadow-sm">


                    <div class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-indigo-50 text-indigo-600">

                        <i class="fa-solid fa-cart-shopping text-3xl"></i>

                    </div>


                    <h2 class="mt-7 text-3xl font-black text-slate-950">
                        Your cart is empty
                    </h2>


                    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">
                        You haven't added any products yet. Browse our products and add the items you need.
                    </p>


                    <a
                        href="/shop"
                        class="mt-7 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700"
                    >

                        <i class="fa-solid fa-store"></i>

                        Browse Products

                    </a>


                </div>

            </div>


        @endif

    </div>

</div>



{{-- =========================================================
     JAVASCRIPT
========================================================== --}}
@if(count($cart))

<script>

document.addEventListener('DOMContentLoaded', function () {


    const csrfToken =
        document.querySelector(
            'meta[name="csrf-token"]'
        )?.getAttribute('content');


    let updateTimer = null;



    /* =========================================================
       MONEY FORMAT
    ========================================================== */

    function formatMoney(value)
    {
        return Number(value).toLocaleString(
            'en-US',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }



    /* =========================================================
       UPDATE SUMMARY
    ========================================================== */

    function updateSummary(
        count,
        subtotal
    )
    {

        const countNumber =
            parseInt(count || 0, 10);


        const subtotalNumber =
            parseFloat(subtotal || 0);


        const badge =
            document.getElementById(
                'cart-count-badge'
            );


        const summaryCount =
            document.getElementById(
                'summary-count'
            );


        const cartSubtotal =
            document.getElementById(
                'cart-subtotal'
            );


        const estimatedTotal =
            document.getElementById(
                'estimated-total'
            );


        if (badge) {

            badge.textContent =
                countNumber +
                ' ' +
                (
                    countNumber === 1
                        ? 'Item'
                        : 'Items'
                );
        }


        if (summaryCount) {

            summaryCount.textContent =
                countNumber;
        }


        if (cartSubtotal) {

            cartSubtotal.textContent =
                '৳ ' +
                formatMoney(
                    subtotalNumber
                );
        }


        if (estimatedTotal) {

            estimatedTotal.textContent =
                '৳ ' +
                formatMoney(
                    subtotalNumber
                );
        }

    }



    /* =========================================================
       UPDATE CART
    ========================================================== */

    function updateCart(
        itemId,
        quantity
    )
    {

        clearTimeout(
            updateTimer
        );


        updateTimer =
            setTimeout(
                async function ()
                {

                    const item =
                        document.querySelector(
                            '[data-cart-item][data-id="' +
                            itemId +
                            '"]'
                        );


                    if (!item) {
                        return;
                    }


                    const input =
                        item.querySelector(
                            '[data-qty]'
                        );


                    quantity =
                        Math.max(
                            0,
                            parseInt(
                                quantity || 0,
                                10
                            )
                        );


                    input.value =
                        quantity;


                    item.classList.add(
                        'opacity-60',
                        'pointer-events-none'
                    );


                    const formData =
                        new FormData();


                    formData.append(
                        '_token',
                        csrfToken || ''
                    );


                    formData.append(
                        'qty[' + itemId + ']',
                        quantity
                    );


                    try {


                        const response =
                            await fetch(
                                '/cart/update',
                                {
                                    method: 'POST',

                                    headers: {
                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'Accept':
                                            'application/json'
                                    },

                                    body:
                                        formData
                                }
                            );


                        if (!response.ok) {
                            throw new Error(
                                'Cart update failed'
                            );
                        }


                        const data =
                            await response.json();


                        if (
                            data.status !==
                            'success'
                        ) {
                            throw new Error(
                                'Cart update failed'
                            );
                        }



                        /* =========================================
                           ZERO = REMOVE
                        ========================================== */

                        if (quantity === 0) {


                            item.style.transition =
                                'opacity .2s ease, transform .2s ease';


                            item.style.opacity =
                                '0';


                            item.style.transform =
                                'translateX(20px)';


                            setTimeout(
                                function ()
                                {

                                    item.remove();


                                    if (
                                        !document.querySelector(
                                            '[data-cart-item]'
                                        )
                                    ) {

                                        window.location.reload();

                                    }

                                },
                                220
                            );

                        }


                        else {


                            /* =====================================
                               ITEM SUBTOTAL
                            ====================================== */

                            const price =
                                parseFloat(
                                    item.dataset.price || 0
                                );


                            const itemTotal =
                                price * quantity;


                            const totalElement =
                                item.querySelector(
                                    '[data-item-total]'
                                );


                            if (totalElement) {

                                totalElement.textContent =
                                    '৳ ' +
                                    formatMoney(
                                        itemTotal
                                    );

                            }


                            item.classList.remove(
                                'opacity-60',
                                'pointer-events-none'
                            );

                        }



                        /* =========================================
                           SUMMARY
                        ========================================== */

                        updateSummary(
                            data.cartCount,
                            data.subtotal
                        );


                    }
                    catch (error)
                    {

                        console.error(
                            error
                        );


                        item.classList.remove(
                            'opacity-60',
                            'pointer-events-none'
                        );


                        alert(
                            'Unable to update cart. Please try again.'
                        );

                    }

                },
                300
            );

    }



    /* =========================================================
       QUANTITY INPUT
    ========================================================== */

    document
        .querySelectorAll('[data-qty]')
        .forEach(
            function (input)
            {

                input.addEventListener(
                    'input',
                    function ()
                    {

                        const item =
                            this.closest(
                                '[data-cart-item]'
                            );


                        if (!item) {
                            return;
                        }


                        updateCart(
                            item.dataset.id,
                            this.value
                        );

                    }
                );


                input.addEventListener(
                    'change',
                    function ()
                    {

                        let value =
                            parseInt(
                                this.value || 0,
                                10
                            );


                        if (
                            isNaN(value) ||
                            value < 0
                        ) {
                            value = 0;
                        }


                        this.value =
                            value;

                    }
                );

            }
        );



    /* =========================================================
       PLUS
    ========================================================== */

    document
        .querySelectorAll('[data-qty-plus]')
        .forEach(
            function (button)
            {

                button.addEventListener(
                    'click',
                    function ()
                    {

                        const item =
                            this.closest(
                                '[data-cart-item]'
                            );


                        if (!item) {
                            return;
                        }


                        const input =
                            item.querySelector(
                                '[data-qty]'
                            );


                        let value =
                            parseInt(
                                input.value || 0,
                                10
                            );


                        if (isNaN(value)) {
                            value = 0;
                        }


                        value++;


                        input.value =
                            value;


                        updateCart(
                            item.dataset.id,
                            value
                        );

                    }
                );

            }
        );



    /* =========================================================
       MINUS
    ========================================================== */

    document
        .querySelectorAll('[data-qty-minus]')
        .forEach(
            function (button)
            {

                button.addEventListener(
                    'click',
                    function ()
                    {

                        const item =
                            this.closest(
                                '[data-cart-item]'
                            );


                        if (!item) {
                            return;
                        }


                        const input =
                            item.querySelector(
                                '[data-qty]'
                            );


                        let value =
                            parseInt(
                                input.value || 0,
                                10
                            );


                        if (isNaN(value)) {
                            value = 0;
                        }


                        value =
                            Math.max(
                                0,
                                value - 1
                            );


                        input.value =
                            value;


                        updateCart(
                            item.dataset.id,
                            value
                        );

                    }
                );

            }
        );



    /* =========================================================
       REMOVE
    ========================================================== */

    document
        .querySelectorAll('[data-remove]')
        .forEach(
            function (button)
            {

                button.addEventListener(
                    'click',
                    async function ()
                    {

                        const item =
                            this.closest(
                                '[data-cart-item]'
                            );


                        if (!item) {
                            return;
                        }


                        const itemId =
                            item.dataset.id;


                        if (
                            !confirm(
                                'Remove this product from your cart?'
                            )
                        ) {
                            return;
                        }


                        item.classList.add(
                            'opacity-60',
                            'pointer-events-none'
                        );


                        try {


                            const response =
                                await fetch(
                                    '/cart/remove/' +
                                    encodeURIComponent(
                                        itemId
                                    ),
                                    {
                                        method:
                                            'GET',

                                        headers: {
                                            'X-Requested-With':
                                                'XMLHttpRequest',

                                            'Accept':
                                                'application/json'
                                        }
                                    }
                                );


                            if (!response.ok) {

                                throw new Error(
                                    'Remove failed'
                                );

                            }


                            const data =
                                await response.json();


                            if (
                                data.status ===
                                'success'
                            ) {


                                item.style.transition =
                                    'opacity .2s ease, transform .2s ease';


                                item.style.opacity =
                                    '0';


                                item.style.transform =
                                    'translateX(20px)';


                                setTimeout(
                                    function ()
                                    {

                                        item.remove();


                                        if (
                                            !document.querySelector(
                                                '[data-cart-item]'
                                            )
                                        ) {

                                            window.location.reload();

                                        }

                                    },
                                    220
                                );


                                updateSummary(
                                    data.cartCount,
                                    data.subtotal
                                );

                            }


                        }
                        catch (error)
                        {

                            console.error(
                                error
                            );


                            item.classList.remove(
                                'opacity-60',
                                'pointer-events-none'
                            );


                            alert(
                                'Unable to remove product. Please try again.'
                            );

                        }

                    }
                );

            }
        );

});

</script>

@endif

@endsection