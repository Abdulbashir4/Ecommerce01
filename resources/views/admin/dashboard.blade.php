@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <style>
        .dashboard-horizontal-scroll { scrollbar-width: thin; }
        .dashboard-horizontal-scroll::-webkit-scrollbar { height: 7px; }
        .dashboard-horizontal-scroll::-webkit-scrollbar-thumb { border-radius: 9999px; background: #cbd5e1; }
        .dashboard-horizontal-scroll::-webkit-scrollbar-track { background: transparent; }
        @media (max-width: 639px) {
            .dashboard-horizontal-scroll { scroll-padding-left: 1rem; }
        }
    </style>
    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.22em] text-indigo-600">Admin overview</p>
            <div class="mt-1 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-black tracking-tight text-slate-950 lg:text-4xl">Dashboard</h1>
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span> Live database
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-500">এক নজরে অর্ডার, সেলস, আয়, স্টক এবং কাস্টমার তথ্য।</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ url('/admin/sales') }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-indigo-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                <i class="fa-solid fa-cash-register mr-2"></i>New Sale
            </a>
            <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50">
                <i class="fa-solid fa-plus mr-2 text-indigo-600"></i>Add Product
            </a>
        </div>
    </div>

    {{-- Primary business metrics --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-slate-50 transition duration-500 group-hover:scale-150"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 truncate text-2xl font-black tracking-tight text-slate-950 lg:text-3xl">{{ $card['value'] }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-400">{{ $card['sub'] }}</p>
                    </div>
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl {{ $card['bg'] }} {{ $card['text'] }} transition duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <i class="{{ $card['icon'] }} text-lg"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Order status snapshot --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-black text-slate-950">Order Snapshot</h2>
                <p class="text-sm text-slate-500">Running, pending, completed and cancelled orders at a glance.</p>
            </div>
            <a href="{{ url('/admin/orders') }}" class="text-sm font-black text-indigo-600 transition hover:text-indigo-800">Manage orders <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <a href="{{ url('/admin/orders?status=Processing') }}" class="group rounded-2xl border border-blue-100 bg-blue-50/70 p-4 transition hover:-translate-y-0.5 hover:bg-blue-50 hover:shadow-md">
                <div class="flex items-center justify-between"><span class="text-xs font-black uppercase tracking-wider text-blue-700">Running</span><i class="fa-solid fa-spinner text-blue-500 transition group-hover:rotate-180"></i></div>
                <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($runningOrders) }}</div>
                <div class="mt-1 text-[11px] font-semibold text-blue-700">Processing {{ number_format($statusCounts['Processing']) }} · Shipped {{ number_format($statusCounts['Shipped']) }}</div>
            </a>
            <a href="{{ url('/admin/orders?status=Pending') }}" class="group rounded-2xl border border-amber-100 bg-amber-50/70 p-4 transition hover:-translate-y-0.5 hover:bg-amber-50 hover:shadow-md">
                <div class="flex items-center justify-between"><span class="text-xs font-black uppercase tracking-wider text-amber-700">Pending</span><i class="fa-regular fa-clock text-amber-500"></i></div>
                <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($statusCounts['Pending']) }}</div>
                <div class="mt-1 text-[11px] font-semibold text-amber-700">Waiting for processing</div>
            </a>
            <a href="{{ url('/admin/orders?status=Completed') }}" class="group rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 transition hover:-translate-y-0.5 hover:bg-emerald-50 hover:shadow-md">
                <div class="flex items-center justify-between"><span class="text-xs font-black uppercase tracking-wider text-emerald-700">Completed</span><i class="fa-solid fa-circle-check text-emerald-500"></i></div>
                <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($statusCounts['Completed']) }}</div>
                <div class="mt-1 text-[11px] font-semibold text-emerald-700">৳ {{ number_format($completedSales, 2) }} sales</div>
            </a>
            <a href="{{ url('/admin/orders?status=Cancelled') }}" class="group rounded-2xl border border-red-100 bg-red-50/70 p-4 transition hover:-translate-y-0.5 hover:bg-red-50 hover:shadow-md">
                <div class="flex items-center justify-between"><span class="text-xs font-black uppercase tracking-wider text-red-700">Cancelled</span><i class="fa-solid fa-circle-xmark text-red-500"></i></div>
                <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($statusCounts['Cancelled']) }}</div>
                <div class="mt-1 text-[11px] font-semibold text-red-700">Cancelled orders</div>
            </a>
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4">
                <div class="flex items-center justify-between"><span class="text-xs font-black uppercase tracking-wider text-indigo-700">This Month</span><i class="fa-solid fa-calendar-days text-indigo-500"></i></div>
                <div class="mt-2 text-2xl font-black text-slate-950">৳ {{ number_format($monthlySales, 2) }}</div>
                <div class="mt-1 text-[11px] font-semibold text-indigo-700">{{ number_format($monthlyOrders) }} completed sales</div>
            </div>
        </div>
    </section>

    <div class="dashboard-horizontal-scroll grid auto-cols-[88vw] grid-flow-col gap-4 overflow-x-auto pb-2 snap-x snap-mandatory sm:auto-cols-auto sm:grid-flow-row sm:grid-cols-2 sm:overflow-visible sm:pb-0 xl:grid-cols-3">
        <div class="col-span-full flex items-center gap-2 text-[11px] font-bold text-slate-400 sm:hidden"><i class="fa-solid fa-arrows-left-right"></i> ডানে-বামে টেনে পুরো অংশ দেখুন</div>
        {{-- Sales chart --}}
        <section class="snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-1 xl:col-span-2">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Sales Chart</h2>
                    <p class="text-sm text-slate-500">Completed sales for the last 12 months.</p>
                </div>
                <div class="rounded-xl bg-slate-50 px-3 py-2 text-right">
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total sales</div>
                    <div class="text-sm font-black text-slate-950">৳ {{ number_format($completedSales, 2) }}</div>
                </div>
            </div>

            <div class="mt-7 overflow-x-auto rounded-2xl bg-slate-950">
                <div class="flex h-72 min-w-[720px] items-end gap-3 px-5 pb-4 pt-6 sm:min-w-0 sm:gap-3">
                @foreach($monthly as $m)
                    <div class="group flex h-full min-w-[34px] flex-1 flex-col items-center justify-end gap-2 sm:min-w-[44px]">
                        <div class="relative flex h-full w-full items-end justify-center">
                            <div class="absolute bottom-full mb-2 hidden whitespace-nowrap rounded-lg bg-white px-2 py-1 text-[10px] font-black text-slate-800 shadow-xl group-hover:block">
                                ৳ {{ number_format($m['amount'], 2) }} · {{ number_format($m['orders']) }} orders
                            </div>
                            <div class="w-full max-w-10 rounded-t-xl bg-gradient-to-t from-indigo-600 via-blue-500 to-cyan-300 shadow-lg shadow-indigo-950/30 transition duration-500 ease-out group-hover:-translate-y-1 group-hover:from-indigo-400 group-hover:to-cyan-200" style="height: {{ $m['height'] }}%"></div>
                        </div>
                        <span class="text-[10px] font-black text-slate-400">{{ $m['label'] }}</span>
                    </div>
                @endforeach
                </div>
            </div>
        </section>

        {{-- Inventory --}}
        <section class="snap-start rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Stock Alert</h2>
                    <p class="text-sm text-slate-500">Products with stock ≤ {{ $lowStockThreshold }}.</p>
                </div>
                <div class="rounded-xl bg-red-50 px-3 py-2 text-center">
                    <div class="text-xl font-black text-red-600">{{ number_format($outOfStockCount) }}</div>
                    <div class="text-[9px] font-black uppercase tracking-wider text-red-500">Out</div>
                </div>
            </div>

            <div class="mt-5 space-y-2">
                @forelse($lowStockProducts as $product)
                    <a href="{{ url('/admin/products/'.$product->product_id.'/edit') }}" class="group flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-red-100 hover:bg-red-50/50">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ (int)$product->stock_qty <= 0 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                            <i class="fa-solid fa-box text-xs"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold text-slate-800">{{ $product->product_name }}</span>
                            <span class="block truncate text-[10px] text-slate-400">{{ $product->sku ?: 'No SKU' }}</span>
                        </span>
                        <span class="text-right">
                            <span class="block text-sm font-black {{ (int)$product->stock_qty <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ number_format((int)$product->stock_qty) }}</span>
                            <span class="text-[9px] font-bold text-slate-400">units</span>
                        </span>
                    </a>
                @empty
                    <div class="rounded-xl bg-emerald-50 p-6 text-center text-sm font-bold text-emerald-700"><i class="fa-solid fa-circle-check mr-1"></i>Stock looks healthy.</div>
                @endforelse
            </div>

            <a href="{{ url('/admin/products') }}" class="mt-4 block text-center text-xs font-black text-indigo-600 hover:text-indigo-800">View all products <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </section>
    </div>

    <div class="dashboard-horizontal-scroll grid auto-cols-[88vw] grid-flow-col gap-4 overflow-x-auto pb-2 snap-x snap-mandatory sm:auto-cols-auto sm:grid-flow-row sm:grid-cols-2 sm:overflow-visible sm:pb-0 xl:grid-cols-3">
        <div class="col-span-full flex items-center gap-2 text-[11px] font-bold text-slate-400 sm:hidden"><i class="fa-solid fa-arrows-left-right"></i> ডানে-বামে টেনে পুরো অংশ দেখুন</div>
        {{-- Recent orders --}}
        <section class="snap-start rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-1 xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Recent Orders</h2>
                    <p class="text-sm text-slate-500">Latest customer activity.</p>
                </div>
                <a href="{{ url('/admin/orders') }}" class="text-sm font-black text-indigo-600 hover:text-indigo-800">View all</a>
            </div>

            <div class="mt-5 overflow-x-auto rounded-xl">
                <table class="w-full min-w-[760px] text-left">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
                            <th class="px-3 py-3">Order</th>
                            <th class="px-3 py-3">Customer</th>
                            <th class="px-3 py-3">Date</th>
                            <th class="px-3 py-3 text-right">Amount</th>
                            <th class="px-3 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $o)
                            @php
                                $statusClass = match($o->order_status) {
                                    'Completed' => 'bg-emerald-100 text-emerald-700',
                                    'Cancelled' => 'bg-red-100 text-red-700',
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-blue-100 text-blue-700',
                                };
                            @endphp
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-3 py-3"><a href="{{ url('/admin/orders/'.$o->order_id) }}" class="font-black text-indigo-600 hover:text-indigo-800">#{{ $o->order_id }}</a></td>
                                <td class="max-w-[190px] truncate px-3 py-3 text-sm font-bold text-slate-700">{{ $o->customer_name ?: 'Walk-in customer' }}</td>
                                <td class="px-3 py-3 text-xs font-medium text-slate-500">{{ $o->created_at?->format('d M Y, h:i A') }}</td>
                                <td class="px-3 py-3 text-right text-sm font-black text-slate-950">৳ {{ number_format($o->total_amount, 2) }}</td>
                                <td class="px-3 py-3 text-right"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black {{ $statusClass }}">{{ $o->order_status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-12 text-center text-sm font-semibold text-slate-400">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Top selling products --}}
        <section class="snap-start rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 class="text-lg font-black text-slate-950">Top Selling Products</h2>
                <p class="text-sm text-slate-500">Based on completed order quantities.</p>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-indigo-100 hover:bg-indigo-50/30">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-slate-950 text-xs font-black text-white">{{ $index + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-bold text-slate-800">{{ $product->product_name }}</div>
                            <div class="mt-1 text-[10px] font-semibold text-slate-400">{{ number_format((int)$product->qty) }} units sold</div>
                        </div>
                        <div class="text-right text-xs font-black text-indigo-600">৳ {{ number_format((float)$product->amount, 2) }}</div>
                    </div>
                @empty
                    <div class="rounded-xl bg-slate-50 p-6 text-center text-sm font-semibold text-slate-400">No completed sales yet.</div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Store totals --}}
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-50 text-sky-600"><i class="fa-solid fa-box"></i></span><div><div class="text-xs font-bold text-slate-400">Products</div><div class="text-2xl font-black text-slate-950">{{ number_format($totalProducts) }}</div></div></div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-50 text-violet-600"><i class="fa-solid fa-users"></i></span><div><div class="text-xs font-bold text-slate-400">Customers</div><div class="text-2xl font-black text-slate-950">{{ number_format($totalCustomers) }}</div></div></div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-triangle-exclamation"></i></span><div><div class="text-xs font-bold text-slate-400">Low Stock</div><div class="text-2xl font-black text-slate-950">{{ number_format($lowStockProducts->count()) }}</div></div></div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-wallet"></i></span><div><div class="text-xs font-bold text-slate-400">Paid Income</div><div class="text-xl font-black text-slate-950">৳ {{ number_format($paidIncome, 2) }}</div></div></div></div>
    </section>
</div>
@endsection
