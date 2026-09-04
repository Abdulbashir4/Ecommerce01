@extends('layouts.app')
@section('content')
<div class="mx-auto w-full max-w-6xl px-3 py-6 sm:px-4 sm:py-8 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div><p class="text-xs font-black uppercase tracking-[0.2em] text-blue-700">Order</p><h1 class="text-3xl font-black sm:text-4xl">Checkout</h1></div>
        @guest
            <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">Guest checkout</span>
        @endguest
    </div>

    <form method="post" action="{{ route('checkout.place') }}" class="mt-6 grid gap-5 sm:mt-8 lg:grid-cols-2 lg:gap-6">
        @csrf
        <div class="space-y-4 rounded-2xl border bg-white p-4 sm:p-6">
            <input name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required class="w-full rounded-xl border px-4 py-3" placeholder="Full name">
            <input name="email" type="email" value="{{ old('email', '') }}" class="w-full rounded-xl border px-4 py-3" placeholder="Email">
            <input name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required class="w-full rounded-xl border px-4 py-3" placeholder="Phone">
            <textarea name="address" required class="min-h-28 w-full rounded-xl border px-4 py-3" placeholder="Address">{{ old('address') }}</textarea>
            <input name="city" value="{{ old('city') }}" required class="w-full rounded-xl border px-4 py-3" placeholder="City">
            <input name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-xl border px-4 py-3" placeholder="Postal code">
            <input name="country" value="{{ old('country', \App\Models\Setting::get('general.default_country', 'Bangladesh')) }}" required class="w-full rounded-xl border px-4 py-3" placeholder="Country">
            <select name="payment_method" class="w-full rounded-xl border px-4 py-3">
                @foreach(['COD','Bkash','Nagad','Card'] as $method)<option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>@endforeach
            </select>
            <button class="w-full rounded-xl bg-blue-700 px-6 py-3 font-bold text-white transition hover:bg-blue-800">Place Order</button>
        </div>

        <div class="h-fit rounded-2xl border bg-white p-4 sm:p-6">
            <h2 class="text-xl font-bold">Order Summary</h2>
            @foreach($cart as $i)
                <div class="flex justify-between gap-3 border-b py-3 text-sm"><span class="min-w-0 break-words">{{ $i['name'] }} × {{ $i['qty'] }}</span><span class="shrink-0">{{ \App\Models\Setting::get('general.currency_symbol','৳') }} {{ number_format($i['price']*$i['qty'],2) }}</span></div>
            @endforeach
            <div class="mt-5 space-y-2 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span>{{ \App\Models\Setting::get('general.currency_symbol','৳') }} {{ number_format($subtotal,2) }}</span></div>
                @if($taxEnabled)<div class="flex justify-between"><span>Tax ({{ rtrim(rtrim(number_format($taxRate,2), '0'), '.') }}%)</span><span>{{ \App\Models\Setting::get('general.currency_symbol','৳') }} {{ number_format($tax,2) }}</span></div>@endif
                @if($shippingEnabled)<div class="flex justify-between"><span>Shipping</span><span>{{ \App\Models\Setting::get('general.currency_symbol','৳') }} {{ number_format($shipping,2) }}</span></div>@endif
            </div>
            <div class="mt-5 flex justify-between gap-4 border-t pt-4 text-xl font-black"><span>Total</span><span>{{ \App\Models\Setting::get('general.currency_symbol','৳') }} {{ number_format($subtotal+$tax+$shipping,2) }}</span></div>
        </div>
    </form>
</div>
@endsection
