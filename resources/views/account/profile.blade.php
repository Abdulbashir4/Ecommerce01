@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')
        <div class="min-w-0">
            <div class="mb-6"><p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Account settings</p><h1 class="mt-1 text-3xl font-black text-slate-950">My Profile</h1><p class="mt-2 text-sm text-slate-500">Keep your customer information up to date.</p></div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        @if($user->profile_image)
                            <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-3xl object-cover ring-4 ring-slate-100">
                        @else
                            <span class="grid h-20 w-20 place-items-center rounded-3xl bg-indigo-600 text-2xl font-black text-white">{{ strtoupper(substr($user->name ?? 'U',0,1)) }}</span>
                        @endif
                        <div><h2 class="font-black text-slate-900">Profile photo</h2><p class="mt-1 text-xs text-slate-500">JPG, PNG, WEBP · maximum 2 MB.</p><label class="mt-3 inline-flex cursor-pointer rounded-xl border border-slate-200 px-4 py-2 text-xs font-black text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-camera mr-2"></i>Choose photo<input type="file" name="profile_image" accept="image/*" class="hidden"></label></div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block"><span class="mb-2 block text-sm font-black text-slate-700">Full name</span><input name="name" value="{{ old('name', $user->name) }}" required maxlength="150" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></label>
                        <label class="block"><span class="mb-2 block text-sm font-black text-slate-700">Phone number</span><input name="phone" value="{{ old('phone', $user->phone) }}" required maxlength="50" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></label>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 text-xs text-slate-500"><i class="fa-solid fa-circle-info mr-1 text-indigo-500"></i> Your phone number is used to sign in to your account.</div>
                    <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-black text-white transition hover:bg-indigo-700 sm:w-auto"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
