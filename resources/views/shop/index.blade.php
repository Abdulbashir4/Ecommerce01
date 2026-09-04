@extends('layouts.app')

@section('content')
@php
    $d = array_replace([
        'layout' => 'grid','mobile_columns' => 1,'tablet_columns' => 2,'desktop_columns' => 4,'gap' => 5,'show_shop_products' => true,
    ], is_array($productDisplay ?? null) ? $productDisplay : []);
    $l = array_replace([
        'container'=>'7xl','section_spacing'=>'normal','sidebar'=>'right','shop_header'=>true,'breadcrumbs'=>true,'pagination'=>true,
    ], is_array($layoutSettings ?? null) ? $layoutSettings : []);
    $mobileCols = (int)$d['mobile_columns'] === 2 ? 'grid-cols-2' : 'grid-cols-1';
    $tabletCols = match((int)$d['tablet_columns']) { 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4', default => 'sm:grid-cols-2' };
    $desktopCols = match((int)$d['desktop_columns']) { 3 => 'lg:grid-cols-3', 5 => 'lg:grid-cols-5', 6 => 'lg:grid-cols-6', default => 'lg:grid-cols-4' };
    $gap = match((int)$d['gap']) { 3 => 'gap-3', 4 => 'gap-4', 6 => 'gap-6', 8 => 'gap-8', default => 'gap-5' };
    $container = match($l['container']) { '5xl'=>'max-w-5xl','6xl'=>'max-w-6xl','full'=>'max-w-none','7xl'=>'max-w-7xl',default=>'max-w-7xl' };
    $space = match($l['section_spacing']) { 'compact'=>'py-5','large'=>'py-12',default=>'py-8' };
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full {{ $container }} px-3 {{ $space }} sm:px-4 lg:px-8">
        @if($l['breadcrumbs'])
            <nav class="mb-4 text-xs font-semibold text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a><span class="mx-2">/</span><span class="text-slate-600">Shop</span>
            </nav>
        @endif

        @if($l['shop_header'])
            <div class="mb-7 flex flex-col gap-4 sm:mb-9 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-blue-600">Optimum Biomedical</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Medical Products</h1>
                    <p class="mt-2 text-sm text-slate-500 sm:text-base">Find quality medical equipment and biomedical products.</p>
                </div>
                @if(method_exists($products, 'total'))<div class="text-sm font-semibold text-slate-500">{{ $products->total() }} Products</div>@endif
            </div>
        @endif

        <form method="GET" class="mb-8 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                <div class="relative"><i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10" placeholder="Search products by name..."></div>
                <button type="submit" class="rounded-xl bg-blue-600 px-7 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"><i class="fa-solid fa-search mr-2"></i>Search</button>
            </div>
        </form>

        @if($d['show_shop_products'])
            <div class="grid gap-6 {{ $l['sidebar'] === 'none' ? '' : 'lg:grid-cols-[minmax(0,1fr)_18rem]' }}">
                @if($l['sidebar'] === 'left')
                    <aside class="order-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:order-none">
                        <h2 class="font-black text-slate-800">Categories</h2>
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('shop') }}" class="block rounded-lg px-3 py-2 text-sm {{ !request('category') && !request('category_id') ? 'bg-blue-50 font-bold text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">All Products</a>
                            @foreach($categories as $category)
                                <a href="{{ route('shop', ['category'=>$category->category_id]) }}" class="block rounded-lg px-3 py-2 text-sm {{ (int)request('category') === $category->category_id ? 'bg-blue-50 font-bold text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">{{ $category->category_name }}</a>
                            @endforeach
                        </div>
                    </aside>
                @endif

                <div class="{{ $l['sidebar'] === 'left' ? 'lg:col-start-2' : '' }}">
                    @if($d['layout'] === 'list')
                        <div class="space-y-5">
                            @forelse($products as $p)<x-product-card :product="$p" />@empty
                                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"><i class="fa-solid fa-box-open text-3xl text-slate-300"></i><h2 class="mt-4 text-xl font-black">No Products Found</h2><p class="mt-2 text-sm text-slate-500">We couldn't find any products matching your search.</p></div>
                            @endforelse
                        </div>
                    @else
                        <div class="grid {{ $mobileCols }} {{ $tabletCols }} {{ $desktopCols }} {{ $gap }}">
                            @forelse($products as $p)<x-product-card :product="$p" />@empty
                                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"><i class="fa-solid fa-box-open text-3xl text-slate-300"></i><h2 class="mt-4 text-xl font-black">No Products Found</h2><p class="mt-2 text-sm text-slate-500">We couldn't find any products matching your search.</p></div>
                            @endforelse
                        </div>
                    @endif
                </div>

                @if($l['sidebar'] === 'right')
                    <aside class="order-first rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:order-none">
                        <h2 class="font-black text-slate-800">Categories</h2>
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('shop') }}" class="block rounded-lg px-3 py-2 text-sm {{ !request('category') && !request('category_id') ? 'bg-blue-50 font-bold text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">All Products</a>
                            @foreach($categories as $category)
                                <a href="{{ route('shop', ['category'=>$category->category_id]) }}" class="block rounded-lg px-3 py-2 text-sm {{ (int)request('category') === $category->category_id ? 'bg-blue-50 font-bold text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">{{ $category->category_name }}</a>
                            @endforeach
                        </div>
                    </aside>
                @endif
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"><i class="fa-solid fa-eye-slash text-4xl text-slate-300"></i><h2 class="mt-4 text-xl font-black">Product display is disabled</h2></div>
        @endif

        @if($l['pagination'] && $products->hasPages())
            <div class="mt-10 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">{{ $products->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
