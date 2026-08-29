@extends('layouts.admin')
@section('content')
<div class="mx-auto max-w-6xl space-y-5">
 <div><p class="text-sm font-black uppercase tracking-widest text-indigo-600">Security / Roles</p><h1 class="text-3xl font-black">{{ $role?'Edit Role':'Create Role' }}</h1></div>
 @if($role && $role->slug==='super-admin')<div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-sm font-semibold text-indigo-800">Super Admin always has every permission and cannot be restricted from this screen.</div>@endif
 <form method="POST" action="{{ $role?route('admin.roles.update',$role):route('admin.roles.store') }}" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
 @csrf @if($role) @method('PUT') @endif
 <div class="grid gap-5 sm:grid-cols-2"><label><span class="mb-1.5 block text-sm font-bold">Role name</span><input name="name" value="{{ old('name',$role->name??'') }}" required class="w-full rounded-xl border px-4 py-3"></label><label><span class="mb-1.5 block text-sm font-bold">Slug</span><input name="slug" value="{{ old('slug',$role->slug??'') }}" required class="w-full rounded-xl border px-4 py-3"></label><label class="sm:col-span-2"><span class="mb-1.5 block text-sm font-bold">Description</span><input name="description" value="{{ old('description',$role->description??'') }}" class="w-full rounded-xl border px-4 py-3"></label></div>
 <div><div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-black">Permissions</h2><button type="button" id="selectAll" class="rounded-lg border px-3 py-2 text-xs font-bold">Select all</button></div><div class="space-y-5">@foreach($permissions as $group=>$items)<section><h3 class="mb-2 text-sm font-black uppercase tracking-widest text-slate-500">{{ $group }}</h3><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@foreach($items as $permission)<label class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id,old('permissions',$role?->permissions->pluck('id')->all()??[]))) class="permission-box mt-1 h-4 w-4"><span><strong class="block text-sm">{{ $permission->name }}</strong><small class="text-slate-500">{{ $permission->slug }}</small></span></label>@endforeach</div></section>@endforeach</div></div>
 <div class="flex justify-end gap-3"><a href="{{ route('admin.roles.index') }}" class="rounded-xl border px-5 py-3 font-bold">Cancel</a><button class="rounded-xl bg-slate-950 px-6 py-3 font-black text-white">Save Role</button></div>
 </form>
</div>
@push('scripts')<script>document.getElementById('selectAll')?.addEventListener('click',()=>document.querySelectorAll('.permission-box').forEach(x=>x.checked=true));</script>@endpush
@endsection
