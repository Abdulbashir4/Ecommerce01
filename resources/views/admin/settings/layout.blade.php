@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <div>
        <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-600">System / Layout</p>
        <h1 class="text-3xl font-black text-slate-900 sm:text-4xl">Layout Settings</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Product listing, responsive columns, page width, sidebar behaviour, spacing এবং shop page controls।</p>
    </div>
    <form method="POST" action="{{ route('admin.settings.layout.update') }}" class="space-y-6">@csrf
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-table-cells-large"></i></span><div><h2 class="font-black">1. Product Listing Layout</h2><p class="text-xs text-slate-500">Grid অথবা list এবং responsive product columns।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach(['grid'=>'Grid Layout','list'=>'List Layout'] as $value=>$label)
                    <label class="cursor-pointer rounded-2xl border-2 border-slate-200 p-5 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"><input type="radio" name="product_layout" value="{{ $value }}" @checked($settings['product_layout']===$value) class="mr-2"><strong>{{ $label }}</strong><p class="mt-1 text-sm text-slate-500">{{ $value === 'grid' ? 'Responsive cards in columns.' : 'Horizontal product rows.' }}</p></label>
                @endforeach
            </div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <label><span class="mb-1.5 block text-sm font-bold">Mobile</span><select name="mobile_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([1,2] as $v)<option value="{{ $v }}" @selected((int)$settings['mobile_columns']===$v)>{{ $v }} Column</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Tablet</span><select name="tablet_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([2,3,4] as $v)<option value="{{ $v }}" @selected((int)$settings['tablet_columns']===$v)>{{ $v }} Columns</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Desktop</span><select name="desktop_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([3,4,5,6] as $v)<option value="{{ $v }}" @selected((int)$settings['desktop_columns']===$v)>{{ $v }} Columns</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Grid Gap</span><select name="gap" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([3,4,5,6,8] as $v)<option value="{{ $v }}" @selected((int)$settings['gap']===$v)>{{ $v }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Text Align</span><select name="text_align" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="left" @selected($settings['text_align']==='left')>Left</option><option value="center" @selected($settings['text_align']==='center')>Center</option></select></label>
            </div>
        </section>
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-sky-50 text-sky-600"><i class="fa-solid fa-maximize"></i></span><div><h2 class="font-black">2. Page Width & Spacing</h2><p class="text-xs text-slate-500">Shop/content container এবং section spacing।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label><span class="mb-1.5 block text-sm font-bold">Content Container</span><select name="container" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach(['5xl'=>'Compact (5XL)','6xl'=>'Medium (6XL)','7xl'=>'Wide (7XL)','full'=>'Full Width'] as $v=>$label)<option value="{{ $v }}" @selected($settings['container']===$v)>{{ $label }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Section Spacing</span><select name="section_spacing" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach(['compact'=>'Compact','normal'=>'Normal','large'=>'Large'] as $v=>$label)<option value="{{ $v }}" @selected($settings['section_spacing']===$v)>{{ $label }}</option>@endforeach</select></label>
            </div>
        </section>
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-sliders"></i></span><div><h2 class="font-black">3. Shop Page Controls</h2><p class="text-xs text-slate-500">Shop page-এর header, breadcrumb, pagination এবং sidebar অবস্থান।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-3">
                <label><span class="mb-1.5 block text-sm font-bold">Sidebar</span><select name="sidebar" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="left" @selected($settings['sidebar']==='left')>Left</option><option value="right" @selected($settings['sidebar']==='right')>Right</option><option value="none" @selected($settings['sidebar']==='none')>Hidden</option></select></label>
                @foreach(['shop_header'=>'Shop Header','breadcrumbs'=>'Breadcrumbs','pagination'=>'Pagination'] as $key=>$label)
                    <label class="flex items-center justify-between rounded-xl border border-slate-200 p-3"><span class="font-bold">{{ $label }}</span><span><input type="hidden" name="{{ $key }}" value="0"><input type="checkbox" name="{{ $key }}" value="1" @checked($settings[$key]) class="h-5 w-5 rounded border-slate-300 text-indigo-600"></span></label>
                @endforeach
            </div>
        </section>
        <div class="sticky bottom-3 z-10 flex justify-end rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-xl backdrop-blur"><button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-7 py-3 text-sm font-black text-white shadow-lg">Save All Layout Settings</button></div>
    </form>
</div>
@endsection
