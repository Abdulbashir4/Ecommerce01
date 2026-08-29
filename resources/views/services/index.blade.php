@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/20 via-transparent to-indigo-500/20"></div>
        <div class="relative mx-auto max-w-7xl px-5 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-white/5 px-4 py-2 text-xs font-bold uppercase tracking-[.18em] text-cyan-200"><i class="fa-solid fa-circle-check"></i> Optimum Biomedical</span>
                <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">{{ $title }}</h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">{{ $subtitle }}</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if($services->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <i class="fa-solid fa-briefcase-medical text-4xl text-slate-300"></i>
                <h2 class="mt-4 text-xl font-black text-slate-900">Services are being updated</h2>
                <p class="mt-2 text-slate-500">Please contact us for current service information.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Contact Us</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($services as $service)
                    <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                            @if($service->image)
                                <img src="{{ asset($service->image) }}" alt="{{ $service->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="grid h-full w-full place-items-center bg-gradient-to-br from-cyan-50 to-indigo-50"><i class="fa-solid {{ $service->icon ?: 'fa-briefcase-medical' }} text-5xl text-cyan-600"></i></div>
                            @endif
                            <span class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1.5 text-xs font-bold text-slate-700 shadow">{{ $type === 'hospital' ? 'Hospital Service' : 'Other Service' }}</span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-start gap-4">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-cyan-50 text-cyan-600"><i class="fa-solid {{ $service->icon ?: 'fa-briefcase-medical' }}"></i></span>
                                <div><h2 class="text-lg font-black text-slate-900">{{ $service->title }}</h2><p class="mt-2 text-sm leading-6 text-slate-500">{{ $service->short_description }}</p></div>
                            </div>
                            <div class="mt-auto pt-6"><a href="{{ route($type === 'hospital' ? 'hospital.services.show' : 'other.services.show', ['slug' => $service->slug]) }}" class="inline-flex items-center text-sm font-black text-indigo-600">Explore service <i class="fa-solid fa-arrow-right ml-2 transition group-hover:translate-x-1"></i></a></div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
