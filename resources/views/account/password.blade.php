@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-xl px-4 py-10">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-black uppercase tracking-widest text-indigo-600">Security</p>
        <h1 class="mt-2 text-3xl font-black">Change Password</h1>
        <form method="POST" action="{{ route('account.password.update') }}" class="mt-6 space-y-4">
            @csrf @method('PUT')
            <label class="block"><span class="mb-1 block text-sm font-bold">Current password</span><input type="password" name="current_password" required class="w-full rounded-xl border px-4 py-3"></label>
            <label class="block"><span class="mb-1 block text-sm font-bold">New password</span><input type="password" name="password" required class="w-full rounded-xl border px-4 py-3"></label>
            <label class="block"><span class="mb-1 block text-sm font-bold">Confirm new password</span><input type="password" name="password_confirmation" required class="w-full rounded-xl border px-4 py-3"></label>
            <button class="w-full rounded-xl bg-slate-950 px-5 py-3 font-black text-white">Change Password</button>
        </form>
    </div>
</div>
@endsection
