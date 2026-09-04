@extends('layouts.app')

@section('content')
@php
    $company = $company ?? \App\Models\CompanyInfo::query()->first();
    $about = $company?->about_us ?: 'Optimum Biomedical provides medical equipment, biomedical products and service support for healthcare organizations.';
@endphp
<div class="bg-slate-50">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        @if($company?->banner)<img src="{{ asset(str_starts_with($company->banner, 'uploads/') ? $company->banner : 'uploads/side_image/'.basename($company->banner)) }}" class="absolute inset-0 h-full w-full object-cover opacity-20" alt="{{ $company?->company_name ?: 'Company' }}">@endif
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/90 to-indigo-950/70"></div>
        <div class="relative mx-auto flex max-w-7xl flex-col gap-8 px-5 py-16 sm:px-6 md:flex-row md:items-center lg:px-8 lg:py-20">
            <div class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-3xl border border-white/10 bg-white shadow-2xl sm:h-28 sm:w-28">
                @if($company?->logo)<img src="{{ asset(str_starts_with($company->logo, 'uploads/') ? $company->logo : 'uploads/side_image/'.basename($company->logo)) }}" alt="Logo" class="h-full w-full object-contain p-3">@else<i class="fa-solid fa-hospital text-4xl text-cyan-600"></i>@endif
            </div>
            <div><p class="text-sm font-bold uppercase tracking-[.2em] text-cyan-300">Company Profile</p><h1 class="mt-2 text-3xl font-black sm:text-4xl lg:text-5xl">{{ $company?->company_name ?: 'Optimum Biomedical' }}</h1><p class="mt-4 max-w-2xl text-slate-300">Medical equipment, biomedical products and professional service support.</p></div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-7 lg:grid-cols-[1.4fr_.6fr]">
            <article class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm sm:p-9"><span class="text-xs font-black uppercase tracking-[.18em] text-indigo-600">About us</span><h2 class="mt-3 text-2xl font-black text-slate-900">Healthcare support built around reliability</h2><div class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $about }}</div></article>
            <aside class="rounded-3xl bg-slate-950 p-7 text-white shadow-sm"><h2 class="text-lg font-black">Get in touch</h2><div class="mt-6 space-y-5 text-sm">@if($company?->phone)<a href="tel:{{ $company->phone }}" class="flex gap-3"><i class="fa-solid fa-phone mt-1 text-cyan-300"></i><span>{{ $company->phone }}</span></a>@endif @if($company?->email)<a href="mailto:{{ $company->email }}" class="flex gap-3 break-all"><i class="fa-solid fa-envelope mt-1 text-cyan-300"></i><span>{{ $company->email }}</span></a>@endif @if($company?->address)<div class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-cyan-300"></i><span>{{ $company->address }}</span></div>@endif</div><a href="{{ route('contact') }}" class="mt-7 inline-flex rounded-xl bg-white px-5 py-3 text-sm font-black text-slate-900">Contact Us</a></aside>
        </div>
    </section>
</div>
@endsection
