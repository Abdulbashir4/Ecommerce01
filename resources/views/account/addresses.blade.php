@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')
        <div class="min-w-0">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Delivery</p><h1 class="mt-1 text-3xl font-black text-slate-950">My Addresses</h1><p class="mt-2 text-sm text-slate-500">Save multiple delivery addresses and choose a default.</p></div>
                <a href="#add-address" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white hover:bg-indigo-700"><i class="fa-solid fa-plus"></i>Add address</a>
            </div>

            <div class="space-y-4">
                @forelse($addresses as $address)
                    <div class="rounded-3xl border {{ $address->is_default ? 'border-indigo-200 ring-2 ring-indigo-50' : 'border-slate-200' }} bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2"><h2 class="font-black text-slate-950">{{ $address->label }}</h2>@if($address->is_default)<span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-indigo-700">Default</span>@endif</div>
                                <p class="mt-2 font-bold text-slate-800">{{ $address->recipient_name }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $address->phone }}</p>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $address->address }}<br>{{ $address->city }}{{ $address->postal_code ? ', '.$address->postal_code : '' }}<br>{{ $address->country }}</p>
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2">
                                @if(!$address->is_default)
                                    <form method="POST" action="{{ route('account.addresses.default', $address) }}">@csrf @method('PATCH')<button class="rounded-xl border border-indigo-200 px-3 py-2 text-xs font-black text-indigo-700 hover:bg-indigo-50">Make default</button></form>
                                @endif
                                <details class="relative">
                                    <summary class="cursor-pointer list-none rounded-xl border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50">Edit</summary>
                                    <div class="absolute right-0 z-20 mt-2 w-[min(30rem,90vw)] rounded-3xl border border-slate-200 bg-white p-5 shadow-2xl">
                                        @include('account.partials.address-form', ['address' => $address])
                                    </div>
                                </details>
                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Remove this address?')">@csrf @method('DELETE')<button class="rounded-xl border border-red-100 px-3 py-2 text-xs font-black text-red-600 hover:bg-red-50">Remove</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center"><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-location-dot text-xl"></i></span><h2 class="mt-4 font-black">No saved addresses</h2><p class="mt-1 text-sm text-slate-500">Add your delivery address for faster checkout.</p></div>
                @endforelse
            </div>

            <div id="add-address" class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <h2 class="text-xl font-black text-slate-950">Add a new address</h2>
                <p class="mt-1 text-sm text-slate-500">This address is saved to your account.</p>
                <div class="mt-5">@include('account.partials.address-form', ['address' => null])</div>
            </div>
        </div>
    </div>
</div>
@endsection
