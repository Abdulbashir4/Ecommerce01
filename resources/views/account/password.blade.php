@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')
        <div class="min-w-0">
            <div class="mb-6"><p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Security</p><h1 class="mt-1 text-3xl font-black text-slate-950">Change Password</h1><p class="mt-2 text-sm text-slate-500">Use a strong password that you do not reuse elsewhere.</p></div>
            <div class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <form method="POST" action="{{ route('account.password.update') }}" class="space-y-5">
                    @csrf @method('PUT')
                    <label class="block"><span class="mb-2 block text-sm font-black">Current password</span><input type="password" name="current_password" required autocomplete="current-password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></label>
                    <label class="block"><span class="mb-2 block text-sm font-black">New password</span><input type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></label>
                    <label class="block"><span class="mb-2 block text-sm font-black">Confirm new password</span><input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></label>
                    <div class="rounded-2xl bg-indigo-50 p-4 text-xs text-indigo-800"><i class="fa-solid fa-shield-halved mr-1"></i> Minimum 8 characters. After changing it, you can continue using your current session.</div>
                    <button class="w-full rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-black text-white hover:bg-indigo-700 sm:w-auto">Update password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
