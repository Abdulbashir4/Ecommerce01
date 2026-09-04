@extends('layouts.admin')
@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <div><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Store Settings</p><h1 class="text-3xl font-black">Company Information</h1><p class="mt-1 text-sm text-slate-500">Logo, banner and homepage slider images are saved in the public uploads folder.</p></div>

    @if(session('success'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

    <form method="POST" action="{{ url('/admin/company') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-black">Business Details</h2>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label><span class="mb-2 block text-sm font-bold">Company name</span><input name="company_name" value="{{ old('company_name',$company->company_name) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">Phone</span><input name="phone" value="{{ old('phone',$company->phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">Email</span><input name="email" type="email" value="{{ old('email',$company->email) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">Map location</span><input name="map_location" value="{{ old('map_location',$company->map_location) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label class="md:col-span-2"><span class="mb-2 block text-sm font-bold">Address</span><textarea name="address" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('address',$company->address) }}</textarea></label>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-black">Branding</h2>
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <label><span class="mb-2 block text-sm font-bold">Logo</span><input type="file" name="logo" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3">@if($company->logo)<img src="{{ str_starts_with($company->logo, 'uploads/') ? asset($company->logo) : asset('uploads/side_image/'.basename($company->logo)) }}" class="mt-3 h-16 rounded-xl object-contain" onerror="this.style.display='none'">@endif</label>
                <label><span class="mb-2 block text-sm font-bold">Banner</span><input type="file" name="banner" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3">@if($company->banner)<img src="{{ str_starts_with($company->banner, 'uploads/') ? asset($company->banner) : asset('uploads/side_image/'.basename($company->banner)) }}" class="mt-3 h-16 w-full rounded-xl object-cover" onerror="this.style.display='none'">@endif</label>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-black">Homepage Slider</h2>
            <p class="mt-1 text-sm text-slate-500">New images are appended; existing slider images are kept.</p>
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <label><span class="mb-2 block text-sm font-bold">Main slider images</span><input type="file" name="slider_images[]" multiple accept="image/*" class="w-full rounded-xl border border-dashed border-indigo-300 bg-indigo-50/40 p-4"></label>
                <label><span class="mb-2 block text-sm font-bold">Right promo images</span><input type="file" name="right_slider_images[]" multiple accept="image/*" class="w-full rounded-xl border border-dashed border-sky-300 bg-sky-50/40 p-4"></label>
            </div>
            @php $sliderFiles = is_array($company->slider_image) ? $company->slider_image : []; $rightFiles = is_array($company->right_slider) ? $company->right_slider : []; @endphp
            <div class="mt-5 grid gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach($sliderFiles as $img)<img src="{{ str_starts_with($img, 'uploads/') ? asset($img) : asset('uploads/side_image/'.basename($img)) }}" class="aspect-video w-full rounded-xl object-cover" onerror="this.style.display='none'">@endforeach
                @foreach($rightFiles as $img)<img src="{{ str_starts_with($img, 'uploads/') ? asset($img) : asset('uploads/side_image/'.basename($img)) }}" class="aspect-video w-full rounded-xl object-cover" onerror="this.style.display='none'">@endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-black">Social & About</h2>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label><span class="mb-2 block text-sm font-bold">Facebook</span><input name="facebook" value="{{ old('facebook',$company->facebook) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">YouTube</span><input name="youtube" value="{{ old('youtube',$company->youtube) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label class="md:col-span-2"><span class="mb-2 block text-sm font-bold">About us</span><textarea name="about_us" rows="6" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('about_us',$company->about_us) }}</textarea></label>
            </div>
        </section>

        <div class="flex justify-end"><button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-7 py-3 font-black text-white shadow-lg">Save Company Info</button></div>
    </form>
</div>
@endsection
