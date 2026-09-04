@extends('layouts.admin')
@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div><p class="text-sm font-black uppercase tracking-widest text-indigo-600">Security</p><h1 class="text-3xl font-black">Users</h1><p class="mt-1 text-sm text-slate-500">Manage accounts, roles, status and password resets.</p></div>
        <div class="flex gap-2"><a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black">Roles & Permissions</a><a href="{{ route('admin.users.create') }}" class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-5 py-3 text-sm font-black text-white shadow-lg"><i class="fa-solid fa-plus mr-2"></i>Add User</a></div>
    </div>

    <form class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_180px_180px_auto]">
        <input name="q" value="{{ request('q') }}" placeholder="Search name or phone..." class="rounded-xl border border-slate-200 px-4 py-3 text-sm">
        <select name="status" class="rounded-xl border border-slate-200 px-4 py-3 text-sm"><option value="">All status</option>@foreach(['active','inactive','blocked'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select>
        <select name="role" class="rounded-xl border border-slate-200 px-4 py-3 text-sm"><option value="">All roles</option>@foreach($roles as $role)<option value="{{ $role->slug }}" @selected(request('role')===$role->slug)>{{ $role->name }}</option>@endforeach</select>
        <button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white">Filter</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="p-4">User</th><th class="p-4">Phone</th><th class="p-4">Roles</th><th class="p-4">Status</th><th class="p-4">Last Login</th><th class="p-4 text-right">Actions</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($users as $user)
            <tr class="hover:bg-indigo-50/30">
                <td class="p-4"><div class="font-black">{{ $user->name }} <span class="text-xs font-normal text-slate-400">#{{ $user->id }}</span></div><div class="mt-1 text-xs text-slate-400">{{ $user->force_password_change ? 'Password change required' : 'Normal login' }}</div></td>
                <td class="p-4">{{ $user->phone }}</td>
                <td class="p-4"><div class="flex flex-wrap gap-1">@forelse($user->roles as $role)<span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $role->name }}</span>@empty<span class="text-xs text-slate-400">No role</span>@endforelse</div></td>
                <td class="p-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $user->status==='active'?'bg-emerald-50 text-emerald-700':($user->status==='blocked'?'bg-red-50 text-red-700':'bg-amber-50 text-amber-700') }}">{{ ucfirst($user->status ?: 'active') }}</span></td>
                <td class="p-4 text-xs text-slate-500">{{ $user->last_login_at?->format('d M Y, h:i A') ?? 'Never' }}</td>
                <td class="p-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.users.edit',$user) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-indigo-600">Edit</a><form method="POST" action="{{ route('admin.users.status',$user) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $user->status==='active'?'inactive':'active' }}"><button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold">{{ $user->status==='active'?'Disable':'Enable' }}</button></form><form method="POST" action="{{ route('admin.users.delete',$user) }}" onsubmit="return confirm('Delete this user permanently?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-100 px-3 py-2 text-xs font-bold text-red-600">Delete</button></form></div></td>
            </tr>
            @empty<tr><td colspan="6" class="p-12 text-center text-slate-500">No users found.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
    {{ $users->links() }}
</div>
@endsection
