@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-3xl px-4 py-16 text-center">
    <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-emerald-100 text-emerald-600"><i class="fa-solid fa-check text-3xl"></i></div>
    <p class="mt-7 text-xs font-black uppercase tracking-[0.2em] text-emerald-600">Order received</p>
    <h1 class="mt-2 text-4xl font-black text-slate-900">Thank you for your order!</h1>
    <p class="mt-4 text-slate-500">Your order #{{ $order->order_id }} has been created successfully.</p>
    <div class="mx-auto mt-8 max-w-xl rounded-3xl border bg-white p-6 text-left shadow-sm">
        <div class="flex justify-between"><span class="font-bold">Order status</span><span>{{ $order->order_status }}</span></div>
        <div class="mt-3 flex justify-between"><span class="font-bold">Payment</span><span>{{ $order->payment_method }}</span></div>
        <div class="mt-3 flex justify-between text-lg"><span class="font-black">Total</span><span class="font-black">{{ \App\Models\Setting::get('general.currency_symbol','৳') }} {{ number_format($order->total_amount,2) }}</span></div>
    </div>
    <a href="{{ url('/shop') }}" class="mt-7 inline-flex rounded-xl bg-slate-950 px-6 py-3 font-black text-white">Continue Shopping</a>
</div>
@endsection
