<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Admin Panel — Optimum Biomedical' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        html { scroll-behavior:smooth; }
        body { min-width: 320px; }
        .admin-scroll::-webkit-scrollbar{width:7px;height:7px}.admin-scroll::-webkit-scrollbar-thumb{background:#334155;border-radius:999px}
        .admin-scroll::-webkit-scrollbar-track{background:transparent}
        .admin-table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;overscroll-behavior-x:contain}
        .admin-table-wrap table{min-width:max-content}
        @media (max-width:767px){
            .admin-scroll{width:min(20rem,92vw)}
            main{overflow-x:hidden}
            input,select,textarea,button{max-width:100%}
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<header class="fixed inset-x-0 top-0 z-50 h-16 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="flex h-full min-w-0 items-center justify-between gap-2 px-2.5 sm:px-4 lg:px-6">
        <div class="flex min-w-0 items-center gap-2 sm:gap-3">
            <button id="menuToggle" type="button" class="rounded-xl p-2 text-slate-700 transition hover:bg-slate-100 md:hidden"><i class="fa-solid fa-bars text-xl"></i></button>
            <a href="{{ url('/admin') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-lg shadow-indigo-200"><i class="fa-solid fa-shield-halved"></i></span>
                <span class="hidden sm:block"><span class="block text-sm font-black tracking-tight">Optimum Biomedical</span><span class="block text-xs font-medium text-slate-500">Admin Panel</span></span>
            </a>
        </div>
        <div class="flex min-w-0 items-center gap-1.5 sm:gap-3">
            <a href="{{ url('/') }}" target="_blank" class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 sm:inline-flex"><i class="fa-solid fa-store mr-2"></i>Visit Store</a>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-1.5">
                @php($avatar = auth()->user()->profile_image)
                @if($avatar)
                    <img src="{{ asset($avatar) }}" class="h-9 w-9 rounded-full object-cover ring-2 ring-white" alt="Admin">
                @else
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-indigo-600 text-sm font-black text-white">{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}</span>
                @endif
                <div class="hidden leading-tight sm:block"><div class="text-sm font-bold">{{ auth()->user()->name }}</div><div class="text-xs text-slate-500">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : (auth()->user()->roles->pluck('name')->join(', ') ?: 'Admin') }}</div></div>
            </div>
            <form method="POST" action="{{ url('/logout') }}">@csrf<button class="rounded-xl p-2.5 text-slate-500 transition hover:bg-red-50 hover:text-red-600" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button></form>
        </div>
    </div>
</header>
<div class="flex pt-16">
    <aside id="sidebar" class="admin-scroll fixed inset-y-16 left-0 z-40 w-[min(18rem,88vw)] -translate-x-full overflow-y-auto bg-slate-950 p-4 text-slate-200 shadow-2xl transition-transform duration-300 md:sticky md:top-16 md:h-[calc(100vh-4rem)] md:translate-x-0 md:shadow-none">
        <nav class="space-y-2">
            <a href="{{ url('/admin') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition {{ request()->is('admin') ? 'bg-gradient-to-r from-indigo-600 to-sky-500 text-white shadow-lg shadow-indigo-950/30' : 'hover:bg-white/10' }}"><i class="fa-solid fa-gauge w-5 text-center"></i>Dashboard</a>
            <button data-sub="productsMenu" class="flex w-full items-center justify-between rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10"><span class="flex items-center gap-3"><i class="fa-solid fa-box w-5 text-center"></i>Products</span><i class="fa-solid fa-chevron-down text-xs transition" data-chevron></i></button>
            <div id="productsMenu" class="ml-4 space-y-1 border-l border-white/10 pl-3 {{ request()->is('admin/products*') || request()->is('admin/catalog*') ? '' : 'hidden' }}">
                <a href="{{ url('/admin/products/create') }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-white/10"><i class="fa-solid fa-plus mr-2"></i>Add Product</a>
                <a href="{{ url('/admin/products') }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-white/10"><i class="fa-solid fa-list mr-2"></i>Product List</a>
                <a href="{{ url('/admin/catalog') }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-white/10"><i class="fa-solid fa-layer-group mr-2"></i>Category / Brand</a>
                <a href="{{ url('/admin/sales') }}" class="block rounded-lg px-3 py-2 text-sm font-bold transition {{ request()->is('admin/sales*') ? 'bg-gradient-to-r from-emerald-600/90 to-teal-500/90 text-white shadow-lg shadow-emerald-950/20' : 'hover:bg-white/10' }}"><i class="fa-solid fa-receipt mr-2"></i>Product Sale</a>
            </div>
            <button data-sub="ordersMenu" class="flex w-full items-center justify-between rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10"><span class="flex items-center gap-3"><i class="fa-solid fa-cart-shopping w-5 text-center"></i>Orders</span><i class="fa-solid fa-chevron-down text-xs"></i></button>
            <div id="ordersMenu" class="ml-4 space-y-1 border-l border-white/10 pl-3 {{ request()->is('admin/orders*') || request()->is('admin/order*') ? '' : 'hidden' }}">
                <a href="{{ url('/admin/orders') }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-white/10"><i class="fa-solid fa-spinner mr-2"></i>Order Running</a>
                <a href="{{ url('/admin/orders?status=Completed') }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-white/10"><i class="fa-solid fa-circle-check mr-2"></i>Order Complete</a>
                <a href="{{ url('/admin/tracking') }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-white/10"><i class="fa-solid fa-location-dot mr-2"></i>Order Tracking</a>
            </div>
            <a href="{{ url('/admin/company') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10"><i class="fa-solid fa-building w-5 text-center"></i>Company Info</a>
            <div class="my-4 border-t border-white/10">
            </div>
         <a href="{{ route('admin.settings') }}"
   class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10">
    <i class="fa-solid fa-gear w-5 text-center"></i>
    Settings
</a>
@if(auth()->user()->hasPermission('users.view'))
<a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10"><i class="fa-solid fa-users w-5 text-center"></i>Users</a>
@endif
@if(auth()->user()->hasPermission('roles.view'))
<a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10"><i class="fa-solid fa-user-shield w-5 text-center"></i>Roles & Permissions</a>
@endif
@if(auth()->user()->hasPermission('audit.view'))
<a href="{{ route('admin.audit.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition hover:bg-white/10"><i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>Audit Log</a>
@endif
        </nav>
    </aside>
    <div id="backdrop" class="pointer-events-none fixed inset-0 z-30 bg-slate-950/60 opacity-0 transition md:hidden"></div>
    <main class="min-w-0 flex-1 overflow-x-hidden p-2.5 sm:p-4 lg:p-7">
        @if(session('success'))<div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">@foreach($errors->all() as $error)<div><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $error }}</div>@endforeach</div>@endif
        @yield('content')
    </main>
</div>
<script>
(()=>{const t=document.getElementById('menuToggle'),s=document.getElementById('sidebar'),b=document.getElementById('backdrop');const close=()=>{s.classList.add('-translate-x-full');b.classList.remove('opacity-100');b.classList.add('opacity-0');b.classList.add('pointer-events-none')};t?.addEventListener('click',()=>{s.classList.toggle('-translate-x-full');b.classList.toggle('opacity-100');b.classList.toggle('opacity-0');b.classList.toggle('pointer-events-none')});b?.addEventListener('click',close);document.querySelectorAll('[data-sub]').forEach(x=>x.addEventListener('click',()=>document.getElementById(x.dataset.sub)?.classList.toggle('hidden')));document.querySelectorAll('#sidebar a').forEach(a=>a.addEventListener('click',()=>{if(innerWidth<768)close()}));})();
</script>
@stack('scripts')
</body></html>
