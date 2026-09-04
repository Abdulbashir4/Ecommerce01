@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Customer account</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Welcome back, {{ $user->name }} 👋</h1>
            <p class="mt-2 text-sm text-slate-500">Manage your orders, profile, addresses and saved products from one place.</p>
        </div>
        <a href="{{ route('account.orders') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-indigo-700">
            <i class="fa-solid fa-box-open"></i> View all orders
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')

        <div class="min-w-0 space-y-6">
            @if($user->force_password_change)
                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                        <div class="min-w-0">
                            <p class="font-black">Password change required</p>
                            <p class="mt-1 text-sm">For your account security, please update your password before continuing.</p>
                            <a href="{{ route('account.password.edit') }}" class="mt-3 inline-flex rounded-xl bg-amber-700 px-4 py-2 text-xs font-black text-white">Change password</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                @foreach([
                    ['total','Total Orders','fa-boxes-stacked','text-indigo-600','bg-indigo-50'],
                    ['pending','Pending','fa-clock','text-amber-600','bg-amber-50'],
                    ['processing','Processing','fa-gears','text-blue-600','bg-blue-50'],
                    ['shipped','Shipped','fa-truck-fast','text-violet-600','bg-violet-50'],
                    ['completed','Completed','fa-circle-check','text-emerald-600','bg-emerald-50'],
                    ['wishlist','Wishlist','fa-heart','text-rose-600','bg-rose-50'],
                    ['reviews','Reviews','fa-star','text-orange-600','bg-orange-50'],
                    ['addresses','Addresses','fa-location-dot','text-cyan-600','bg-cyan-50'],
                ] as [$key,$label,$icon,$color,$bg])
                    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="grid h-10 w-10 place-items-center rounded-2xl {{ $bg }} {{ $color }}"><i class="fa-solid {{ $icon }}"></i></span>
                            <span class="text-2xl font-black text-slate-950">{{ $stats[$key] }}</span>
                        </div>
                        <p class="mt-3 text-xs font-black uppercase tracking-wider text-slate-400">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Recent Orders</h2>
                        <p class="mt-1 text-sm text-slate-500">Your latest purchases and their current status.</p>
                    </div>
                    <a href="{{ route('account.orders') }}" class="text-sm font-black text-indigo-600 hover:text-indigo-800">View all <i class="fa-solid fa-arrow-right ml-1"></i></a>
                </div>

                @forelse($orders as $order)
                    <a href="{{ route('account.order', $order) }}" class="flex flex-col gap-3 border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-slate-100 text-slate-600"><i class="fa-solid fa-receipt"></i></span>
                            <div class="min-w-0">
                                <p class="truncate font-black text-slate-900">Order #{{ $order->order_id }}</p>
                                <p class="text-xs text-slate-500">{{ optional($order->created_at)->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            @php($statusClass = match($order->order_status) {
                                'Completed' => 'bg-emerald-50 text-emerald-700',
                                'Cancelled' => 'bg-red-50 text-red-700',
                                'Shipped' => 'bg-violet-50 text-violet-700',
                                'Processing' => 'bg-blue-50 text-blue-700',
                                default => 'bg-amber-50 text-amber-700',
                            })
                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">{{ $order->order_status }}</span>
                            <span class="font-black text-slate-950">৳ {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-10 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-box-open text-xl"></i></span>
                        <h3 class="mt-4 font-black text-slate-900">No orders yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Your completed purchases will appear here.</p>
                        <a href="{{ route('shop') }}" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white">Start shopping</a>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</div>
@endsection
