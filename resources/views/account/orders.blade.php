@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')
        <div class="min-w-0">
            <div class="mb-6">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Purchase history</p>
                <h1 class="mt-1 text-3xl font-black text-slate-950">My Orders</h1>
                <p class="mt-2 text-sm text-slate-500">Review every order and open its full details.</p>
            </div>

            <div class="mb-5 flex gap-2 overflow-x-auto pb-1">
                <a href="{{ route('account.orders') }}" class="shrink-0 rounded-full px-4 py-2 text-xs font-black {{ !$activeStatus ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600' }}">All</a>
                @foreach($statuses as $status)
                    <a href="{{ route('account.orders', ['status' => $status]) }}" class="shrink-0 rounded-full px-4 py-2 text-xs font-black {{ $activeStatus === $status ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $status }}</a>
                @endforeach
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                @forelse($orders as $order)
                    @php($statusClass = match($order->order_status) {
                        'Completed' => 'bg-emerald-50 text-emerald-700',
                        'Cancelled' => 'bg-red-50 text-red-700',
                        'Shipped' => 'bg-violet-50 text-violet-700',
                        'Processing' => 'bg-blue-50 text-blue-700',
                        default => 'bg-amber-50 text-amber-700',
                    })
                    <div class="flex flex-col gap-4 border-b border-slate-100 p-5 last:border-b-0 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-receipt"></i></span>
                            <div class="min-w-0">
                                <a href="{{ route('account.order', $order) }}" class="font-black text-slate-950 hover:text-indigo-600">Order #{{ $order->order_id }}</a>
                                <p class="mt-1 text-xs text-slate-500">{{ optional($order->created_at)->format('d M Y, h:i A') }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $order->payment_method ?: 'Payment method not specified' }} · {{ $order->payment_status }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">{{ $order->order_status }}</span>
                            <span class="font-black text-slate-950">৳ {{ number_format($order->total_amount, 2) }}</span>
                            <a href="{{ route('account.order', $order) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-black text-slate-700 hover:border-indigo-200 hover:text-indigo-700">View details</a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <span class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-slate-100 text-slate-400"><i class="fa-solid fa-box-open text-2xl"></i></span>
                        <h2 class="mt-4 text-lg font-black">No matching orders</h2>
                        <p class="mt-1 text-sm text-slate-500">Try another status or start shopping.</p>
                    </div>
                @endforelse
            </div>

            @if($orders->hasPages())
                <div class="mt-5">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
