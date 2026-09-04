@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700 shadow-sm">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <p class="font-bold">Order placed successfully!</p>
                <p class="text-sm text-emerald-600">Thank you for your purchase.</p>
            </div>
        </div>
    @endif


    {{-- Order Header --}}
    <div class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 px-6 py-7 text-white sm:px-8">

            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-300">
                        <i class="fa-solid fa-receipt"></i>
                        Order Details
                    </div>

                    <h1 class="text-3xl font-black sm:text-4xl">
                        Order #{{ $order->order_id }}
                    </h1>

                    @if($order->created_at)
                        <p class="mt-2 text-sm text-slate-300">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </p>
                    @endif
                </div>


                {{-- Status --}}
                <div class="flex flex-wrap gap-3 md:justify-end">

                    @php
                        $orderStatus = strtolower($order->order_status ?? '');
                        $paymentStatus = strtolower($order->payment_status ?? '');

                        $orderStatusClass = match($orderStatus) {
                            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'shipped' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-amber-100 text-amber-700 border-amber-200',
                        };

                        $paymentStatusClass = match($paymentStatus) {
                            'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'refunded' => 'bg-purple-100 text-purple-700 border-purple-200',
                            default => 'bg-red-100 text-red-700 border-red-200',
                        };
                    @endphp

                    <div class="rounded-2xl border px-4 py-3 {{ $orderStatusClass }}">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-70">
                            Order Status
                        </p>
                        <p class="mt-1 flex items-center gap-2 font-black">
                            <i class="fa-solid fa-truck-fast"></i>
                            {{ $order->order_status }}
                        </p>
                    </div>

                    <div class="rounded-2xl border px-4 py-3 {{ $paymentStatusClass }}">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-70">
                            Payment
                        </p>
                        <p class="mt-1 flex items-center gap-2 font-black">
                            <i class="fa-solid fa-credit-card"></i>
                            {{ $order->payment_status }}
                        </p>
                    </div>

                </div>

            </div>
        </div>


        {{-- Order Summary --}}
        <div class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-3 sm:divide-x sm:divide-y-0">

            <div class="p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Order Number
                </p>
                <p class="mt-1 text-lg font-black text-slate-900">
                    #{{ $order->order_id }}
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Items
                </p>
                <p class="mt-1 text-lg font-black text-slate-900">
                    {{ $order->items->sum('qty') }}
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Total Amount
                </p>
                <p class="mt-1 text-lg font-black text-indigo-600">
                    ৳ {{ number_format($order->total_amount, 2) }}
                </p>
            </div>

        </div>

    </div>


    {{-- Products --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Order Items --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">
                <div>
                    <h2 class="text-xl font-black text-slate-950">
                        Order Items
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Products included in this order
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            </div>


            <div class="divide-y divide-slate-100">

                @foreach($order->items as $i)

                    <div class="group flex gap-4 p-5 transition hover:bg-slate-50 sm:p-6">

                        {{-- Product Icon / Image --}}
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:h-20 sm:w-20">

                            @php($itemImage = $i->product?->thumbnail ?: $i->product?->featured_image)
                            @if($itemImage)
                                <img
                                    src="{{ asset(str_starts_with($itemImage, 'uploads/') ? $itemImage : 'uploads/products/' . $itemImage) }}"
                                    alt="{{ $i->product_name }}"
                                    class="h-full w-full object-contain"
                                    loading="lazy"
                                >
                            @else
                                <i class="fa-solid fa-microscope text-2xl text-slate-300"></i>
                            @endif

                        </div>


                        {{-- Product Info --}}
                        <div class="min-w-0 flex-1">

                            <h3 class="font-bold text-slate-900 sm:text-lg">
                                {{ $i->product_name }}
                            </h3>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">

                                <span>
                                    <i class="fa-solid fa-layer-group mr-1 text-slate-400"></i>
                                    Quantity: {{ $i->qty }}
                                </span>

                                @if(isset($i->price))
                                    <span>
                                        Unit Price:
                                        ৳ {{ number_format($i->price, 2) }}
                                    </span>
                                @endif

                            </div>

                        </div>


                        {{-- Price --}}
                        <div class="shrink-0 text-right">

                            <p class="text-lg font-black text-slate-950">
                                ৳ {{ number_format($i->total, 2) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Item Total
                            </p>
                            @if($i->product_id && !in_array($i->product_id, $reviewedProductIds ?? [], true))
                                <a href="{{ route('account.reviews') }}" class="mt-2 inline-flex text-xs font-black text-indigo-600 hover:text-indigo-800">Write a review</a>
                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- Total Card --}}
        <div class="h-fit overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="text-xl font-black text-slate-950">
                    Order Summary
                </h2>
            </div>

            <div class="space-y-4 px-6 py-6">

                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">
                        Subtotal
                    </span>

                    <span class="font-bold text-slate-900">
                        ৳ {{ number_format($order->total_amount, 2) }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">
                        Payment Status
                    </span>

                    <span class="font-bold {{ strtolower($order->payment_status) === 'paid' ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $order->payment_status }}
                    </span>
                </div>

                <div class="border-t border-dashed border-slate-200 pt-5">

                    <div class="flex items-end justify-between">

                        <div>
                            <p class="text-sm font-bold text-slate-500">
                                Total
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Including all items
                            </p>
                        </div>

                        <p class="text-2xl font-black text-indigo-600">
                            ৳ {{ number_format($order->total_amount, 2) }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Bottom Buttons --}}
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">

        <a
            href="{{ route('account.orders') }}"
            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3.5 font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Back to My Orders
        </a>

        <a
            href="{{ route('shop') }}"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-sky-500 px-6 py-3.5 font-black text-white shadow-md transition hover:-translate-y-0.5 hover:shadow-lg"
        >
            Continue Shopping
            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>

</div>

@endsection