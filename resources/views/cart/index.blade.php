@extends('layouts.app')

@section('content')

@php
    $cartCount = collect($cart)->sum('qty');

    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += ((float) ($item['price'] ?? 0))
            * ((int) ($item['qty'] ?? 0));
    }
@endphp

<div class="min-h-[70vh] bg-slate-50">

    {{-- Header --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm font-bold text-blue-600">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Shopping Cart
                    </div>

                    <h1 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                        Your Cart
                    </h1>

                    <p class="mt-2 text-sm text-slate-500 sm:text-base">
                        Review your selected biomedical products before checkout.
                    </p>
                </div>

                <div
                    id="cart-count-badge"
                    class="w-fit rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700"
                >
                    {{ $cartCount }} {{ $cartCount == 1 ? 'Item' : 'Items' }}
                </div>

            </div>

        </div>
    </section>


    <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-10 lg:px-8">

        @if(count($cart))

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">

                {{-- CART ITEMS --}}
                <section>

                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
                            <h2 class="text-lg font-black text-slate-950">
                                Cart Items
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Change quantity to automatically update your cart.
                            </p>
                        </div>


                        <div id="cart-items">

                            @foreach($cart as $id => $item)

                                @php
                                    $price = (float) ($item['price'] ?? 0);
                                    $qty = (int) ($item['qty'] ?? 0);
                                    $itemTotal = $price * $qty;

                                    $image = $item['image'] ?? null;

                                    if ($image) {
                                        $imagePath = ltrim(str_replace('\\', '/', $image), '/');

                                        if (
                                            str_starts_with($imagePath, 'uploads/')
                                            || str_starts_with($imagePath, 'storage/')
                                        ) {
                                            $imageUrl = asset($imagePath);
                                        } else {
                                            $imageUrl = asset('uploads/products/' . basename($imagePath));
                                        }
                                    } else {
                                        $imageUrl = null;
                                    }
                                @endphp


                                <div
                                    id="cart-item-{{ $id }}"
                                    data-cart-item
                                    data-id="{{ $id }}"
                                    data-price="{{ $price }}"
                                    class="border-b border-slate-100 p-5 transition sm:p-6"
                                >

                                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                                        {{-- PRODUCT IMAGE --}}
                                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:h-28 sm:w-28">

                                            @if($imageUrl)

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


                                        {{-- PRODUCT INFO --}}
                                        <div class="min-w-0 flex-1">

                                            <h3 class="break-words text-base font-black leading-6 text-slate-950 sm:text-lg">
                                                {{ $item['name'] }}
                                            </h3>

                                            <div class="mt-2">
                                                <span class="text-sm font-bold text-slate-500">
                                                    ৳ {{ number_format($price, 2) }}
                                                </span>

                                                <span class="ml-1 text-xs text-slate-400">
                                                    / unit
                                                </span>
                                            </div>

                                        </div>


                                        {{-- QUANTITY --}}
                                        <div class="flex items-center justify-between gap-5 sm:justify-end">

                                            <div>
                                                <label
                                                    class="mb-1.5 block text-[10px] font-black uppercase tracking-wider text-slate-400"
                                                >
                                                    Quantity
                                                </label>

                                                <div class="flex h-11 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                                                    <button
                                                        type="button"
                                                        data-qty-minus
                                                        class="grid w-10 place-items-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-600"
                                                    >
                                                        <i class="fa-solid fa-minus text-xs"></i>
                                                    </button>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        value="{{ $qty }}"
                                                        name="qty[{{ $id }}]"
                                                        data-qty
                                                        class="w-14 border-x border-slate-200 text-center text-sm font-black text-slate-900 outline-none"
                                                    >

                                                    <button
                                                        type="button"
                                                        data-qty-plus
                                                        class="grid w-10 place-items-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-600"
                                                    >
                                                        <i class="fa-solid fa-plus text-xs"></i>
                                                    </button>

                                                </div>

                                            </div>


                                            {{-- ITEM TOTAL --}}
                                            <div class="min-w-[110px] text-right">

                                                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                                    Subtotal
                                                </div>

                                                <div
                                                    data-item-total
                                                    class="mt-1 text-base font-black text-slate-950"
                                                >
                                                    ৳ {{ number_format($itemTotal, 2) }}
                                                </div>

                                            </div>

                                        </div>


                                        {{-- REMOVE --}}
                                        <button
                                            type="button"
                                            data-remove
                                            class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-red-100 bg-red-50 px-4 text-sm font-bold text-red-600 transition hover:bg-red-100"
                                        >
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                            <span class="sm:hidden">Remove</span>
                                        </button>

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        {{-- Continue Shopping --}}
                        <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-7">

                            <a
                                href="/shop"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                            >
                                <i class="fa-solid fa-arrow-left"></i>
                                Continue Shopping
                            </a>

                        </div>

                    </div>

                </section>


                {{-- ORDER SUMMARY --}}
                <aside class="lg:sticky lg:top-32 lg:self-start">

                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-6 py-5">

                            <div class="flex items-center gap-3">

                                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-100">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>

                                <div>
                                    <h2 class="text-lg font-black text-slate-950">
                                        Order Summary
                                    </h2>

                                    <p class="text-xs text-slate-500">
                                        Your current order total
                                    </p>
                                </div>

                            </div>

                        </div>


                        <div class="space-y-5 p-6">

                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-500">
                                    Items
                                </span>

                                <span
                                    id="summary-count"
                                    class="font-black text-slate-900"
                                >
                                    {{ $cartCount }}
                                </span>
                            </div>


                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-500">
                                    Subtotal
                                </span>

                                <span
                                    id="cart-subtotal"
                                    class="font-black text-slate-900"
                                >
                                    ৳ {{ number_format($subtotal, 2) }}
                                </span>
                            </div>


                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-500">
                                    Delivery
                                </span>

                                <span class="text-right text-xs font-bold text-slate-400">
                                    Calculated at checkout
                                </span>
                            </div>


                            <div class="border-t border-dashed border-slate-200 pt-5">

                                <div class="flex items-end justify-between">

                                    <div>
                                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            Estimated Total
                                        </div>

                                        <div
                                            id="estimated-total"
                                            class="mt-1 text-2xl font-black tracking-tight text-slate-950"
                                        >
                                            ৳ {{ number_format($subtotal, 2) }}
                                        </div>
                                    </div>

                                    <div class="grid h-10 w-10 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                                        <i class="fa-solid fa-check"></i>
                                    </div>

                                </div>

                            </div>


                            <a
                                href="/checkout"
                                id="checkout-button"
                                class="group flex w-full items-center justify-between rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4 text-sm font-black text-white shadow-xl shadow-blue-100 transition hover:-translate-y-0.5 hover:from-blue-700 hover:to-indigo-700"
                            >
                                <span class="flex items-center gap-3">
                                    <i class="fa-solid fa-lock"></i>
                                    Proceed to Checkout
                                </span>

                                <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </a>


                            <div class="flex gap-3 rounded-2xl bg-slate-50 p-4">

                                <div class="mt-0.5 text-emerald-600">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-black text-slate-700">
                                        Secure Checkout
                                    </p>

                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                        Your order information is handled securely during checkout.
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>

        @else

            {{-- EMPTY CART --}}
            <div class="mx-auto max-w-2xl py-10 sm:py-16">

                <div class="rounded-3xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm sm:px-10">

                    <div class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-blue-50 text-blue-600 ring-8 ring-blue-50/70">
                        <i class="fa-solid fa-cart-shopping text-3xl"></i>
                    </div>

                    <h2 class="mt-7 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                        Your cart is empty
                    </h2>

                    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500 sm:text-base">
                        You haven't added any products yet. Explore our biomedical products and add the items you need.
                    </p>

                    <a
                        href="/shop"
                        class="mt-7 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-blue-100 transition hover:-translate-y-0.5 hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-store"></i>
                        Browse Products
                    </a>

                </div>

            </div>

        @endif

    </div>

</div>


{{-- =========================
     AUTO CART UPDATE SCRIPT
     ========================= --}}
@if(count($cart))

<script>
document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let updateTimer = null;


    function formatMoney(value) {
        return Number(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }


    function updateCart(itemId, quantity) {

        clearTimeout(updateTimer);

        updateTimer = setTimeout(async function () {

            const item = document.querySelector(
                '[data-cart-item][data-id="' + itemId + '"]'
            );

            if (!item) {
                return;
            }

            const input = item.querySelector('[data-qty]');

            quantity = Math.max(0, parseInt(quantity || 0, 10));

            input.value = quantity;

            item.classList.add('opacity-60', 'pointer-events-none');

            const formData = new FormData();

            formData.append('_token', csrfToken || '');

            formData.append('qty[' + itemId + ']', quantity);


            try {

                const response = await fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });


                if (!response.ok) {
                    throw new Error('Cart update failed');
                }


                const data = await response.json();


                if (data.status === 'success') {

                    if (quantity === 0) {

                        item.style.transition = 'opacity .2s ease, transform .2s ease';
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';

                        setTimeout(function () {
                            item.remove();

                            if (!document.querySelector('[data-cart-item]')) {
                                window.location.reload();
                            }
                        }, 220);

                    } else {

                        const price = parseFloat(item.dataset.price || 0);

                        const itemTotal = price * quantity;

                        const totalElement = item.querySelector('[data-item-total]');

                        if (totalElement) {
                            totalElement.textContent = '৳ ' + formatMoney(itemTotal);
                        }

                        item.classList.remove('opacity-60', 'pointer-events-none');
                    }


                    updateSummary(
                        data.cartCount,
                        data.subtotal
                    );
                }

            } catch (error) {

                console.error(error);

                item.classList.remove('opacity-60', 'pointer-events-none');

                alert('Unable to update cart. Please try again.');

            }

        }, 350);
    }


    function updateSummary(count, subtotal) {

        const badge = document.getElementById('cart-count-badge');
        const summaryCount = document.getElementById('summary-count');
        const cartSubtotal = document.getElementById('cart-subtotal');
        const estimatedTotal = document.getElementById('estimated-total');

        const numericCount = parseInt(count || 0, 10);

        const countText =
            numericCount + ' ' +
            (numericCount === 1 ? 'Item' : 'Items');


        if (badge) {
            badge.textContent = countText;
        }


        if (summaryCount) {
            summaryCount.textContent = numericCount;
        }


        if (cartSubtotal) {
            cartSubtotal.textContent = '৳ ' + formatMoney(subtotal);
        }


        if (estimatedTotal) {
            estimatedTotal.textContent = '৳ ' + formatMoney(subtotal);
        }
    }


    // Quantity input
    document.querySelectorAll('[data-qty]').forEach(function (input) {

        input.addEventListener('input', function () {

            const item = this.closest('[data-cart-item]');

            if (!item) {
                return;
            }

            const itemId = item.dataset.id;

            updateCart(itemId, this.value);
        });


        input.addEventListener('change', function () {

            let value = parseInt(this.value || 0, 10);

            if (isNaN(value) || value < 0) {
                value = 0;
            }

            this.value = value;
        });

    });


    // Plus buttons
    document.querySelectorAll('[data-qty-plus]').forEach(function (button) {

        button.addEventListener('click', function () {

            const item = this.closest('[data-cart-item]');

            if (!item) {
                return;
            }

            const input = item.querySelector('[data-qty]');

            let value = parseInt(input.value || 0, 10);

            if (isNaN(value)) {
                value = 0;
            }

            value++;

            input.value = value;

            updateCart(item.dataset.id, value);
        });

    });


    // Minus buttons
    document.querySelectorAll('[data-qty-minus]').forEach(function (button) {

        button.addEventListener('click', function () {

            const item = this.closest('[data-cart-item]');

            if (!item) {
                return;
            }

            const input = item.querySelector('[data-qty]');

            let value = parseInt(input.value || 0, 10);

            if (isNaN(value)) {
                value = 0;
            }

            value = Math.max(0, value - 1);

            input.value = value;

            updateCart(item.dataset.id, value);
        });

    });


    // Remove button
    document.querySelectorAll('[data-remove]').forEach(function (button) {

        button.addEventListener('click', async function () {

            const item = this.closest('[data-cart-item]');

            if (!item) {
                return;
            }

            const itemId = item.dataset.id;


            if (!confirm('Remove this product from your cart?')) {
                return;
            }


            item.classList.add('opacity-60', 'pointer-events-none');


            try {

                const response = await fetch('/cart/remove/' + encodeURIComponent(itemId), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });


                if (!response.ok) {
                    throw new Error('Remove failed');
                }


                const data = await response.json();


                if (data.status === 'success') {

                    item.style.transition = 'opacity .2s ease, transform .2s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(20px)';

                    setTimeout(function () {
                        item.remove();

                        if (!document.querySelector('[data-cart-item]')) {
                            window.location.reload();
                        }
                    }, 220);


                    updateSummary(
                        data.cartCount,
                        data.subtotal
                    );
                }

            } catch (error) {

                console.error(error);

                item.classList.remove('opacity-60', 'pointer-events-none');

                alert('Unable to remove this product. Please try again.');
            }

        });

    });

});
</script>

@endif

@endsection