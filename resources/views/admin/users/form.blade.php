@extends('layouts.admin')
@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <div><p class="text-sm font-black uppercase tracking-widest text-indigo-600">Security / Users</p><h1 class="text-3xl font-black">{{ $user ? 'Edit User' : 'Create User' }}</h1></div>
    <form method="POST" action="{{ $user ? route('admin.users.update',$user) : route('admin.users.store') }}" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @if($user) @method('PUT') @endif
        <div class="grid gap-5 sm:grid-cols-2">
            <label><span class="mb-1.5 block text-sm font-bold">Full name</span><input name="name" value="{{ old('name',$user->name??'') }}" required class="w-full rounded-xl border px-4 py-3"></label>
            <label><span class="mb-1.5 block text-sm font-bold">Phone / Login ID</span><input name="phone" value="{{ old('phone',$user->phone??'') }}" required class="w-full rounded-xl border px-4 py-3"></label>
            <label><span class="mb-1.5 block text-sm font-bold">Password {{ $user?'(leave blank to keep current)':'' }}</span><input type="password" name="password" class="w-full rounded-xl border px-4 py-3" {{ $user?'':'required' }}></label>
            <label><span class="mb-1.5 block text-sm font-bold">Confirm password</span><input type="password" name="password_confirmation" class="w-full rounded-xl border px-4 py-3" {{ $user?'':'required' }}></label>
            <label><span class="mb-1.5 block text-sm font-bold">Status</span><select name="status" class="w-full rounded-xl border px-4 py-3">@foreach(['active','inactive','blocked'] as $status)<option value="{{ $status }}" @selected(old('status',$user->status??'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
        </div>
        <label class="flex items-center gap-3 rounded-2xl bg-amber-50 p-4"><input type="checkbox" name="force_password_change" value="1" @checked(old('force_password_change',$user->force_password_change??false)) class="h-5 w-5 rounded"><span><strong class="block">Force password change</strong><small class="text-slate-600">User must change the password after login.</small></span></label>
        <div><div class="mb-3 flex items-center justify-between"><h2 class="text-lg font-black">Roles</h2><span class="text-xs text-slate-500">Permissions are inherited from selected roles.</span></div><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@foreach($roles as $role)<label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 hover:border-indigo-300"><input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id,old('roles',$user?->roles->pluck('id')->all()??[]))) class="mt-1 h-4 w-4"><span><strong class="block">{{ $role->name }}</strong><small class="text-slate-500">{{ $role->description }}</small></span></label>@endforeach</div></div>
        <div class="flex justify-end gap-3"><a href="{{ route('admin.users.index') }}" class="rounded-xl border px-5 py-3 font-bold">Cancel</a><button class="rounded-xl bg-slate-950 px-6 py-3 font-black text-white">{{ $user?'Save Changes':'Create User' }}</button></div>
    </form>
    @if($user)
    <div class="rounded-3xl border border-red-100 bg-red-50/60 p-6"><h2 class="font-black text-red-800">Admin Password Reset</h2><p class="mt-1 text-sm text-red-700">The existing password can never be viewed. Set a new password instead.</p><form method="POST" action="{{ route('admin.users.reset-password',$user) }}" class="mt-4 grid gap-3 sm:grid-cols-3">@csrf @method('PUT')<input type="password" name="password" required placeholder="New password" class="rounded-xl border px-4 py-3"><input type="password" name="password_confirmation" required placeholder="Confirm password" class="rounded-xl border px-4 py-3"><label class="flex items-center gap-2 rounded-xl bg-white px-4"><input type="checkbox" name="force_password_change" value="1" checked> Force change</label><button class="rounded-xl bg-red-600 px-5 py-3 font-black text-white sm:col-span-3">Reset Password</button></form></div>
    @endif
</div>
@endsection
