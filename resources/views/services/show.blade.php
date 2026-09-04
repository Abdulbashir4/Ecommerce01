@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-6 lg:px-8">
        <a href="{{ route($type === 'hospital' ? 'hospital.services' : 'other.services') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left mr-2"></i>Back to services</a>
        <div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="grid lg:grid-cols-2">
                <div class="relative min-h-[280px] bg-slate-100 lg:min-h-[480px]">
                    @if($service->image)
                        <img src="{{ asset($service->image) }}" alt="{{ $service->title }}" class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <div class="grid h-full place-items-center bg-gradient-to-br from-cyan-50 to-indigo-50"><i class="fa-solid {{ $service->icon ?: 'fa-briefcase-medical' }} text-7xl text-cyan-600"></i></div>
                    @endif
                </div>
                <div class="p-7 sm:p-10 lg:p-12">
                    <span class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-cyan-700">{{ $type === 'hospital' ? 'Hospital Bio Medical Service' : 'Other Service' }}</span>
                    <h1 class="mt-5 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">{{ $service->title }}</h1>
                    @if($service->short_description)<p class="mt-4 text-base font-semibold leading-7 text-slate-600">{{ $service->short_description }}</p>@endif
                    @if($service->description)<div class="prose prose-slate mt-6 max-w-none text-sm leading-7">{!! nl2br(e($service->description)) !!}</div>@endif
                    @if(is_array($service->features) && count($service->features))
                        <div class="mt-8"><h2 class="text-sm font-black uppercase tracking-wider text-slate-900">What we provide</h2><ul class="mt-4 space-y-3">@foreach($service->features as $feature)<li class="flex gap-3 text-sm text-slate-600"><i class="fa-solid fa-circle-check mt-1 text-emerald-500"></i><span>{{ $feature }}</span></li>@endforeach</ul></div>
                    @endif
                    <a href="{{ route('contact') }}" class="mt-8 inline-flex items-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:bg-indigo-600">Discuss your requirement <i class="fa-solid fa-arrow-right ml-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
