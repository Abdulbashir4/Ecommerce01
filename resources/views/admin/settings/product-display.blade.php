@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <div>
        <p class="text-sm font-black uppercase tracking-widest text-indigo-600">Settings / Products</p>
        <h1 class="text-3xl font-black text-slate-900 sm:text-4xl">Product Display Control</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">এখান থেকে Home এবং Shop-এর product card-এর layout, image, information, badge, button, spacing, shadow, responsive columns এবং অন্যান্য visual style এক জায়গা থেকে নিয়ন্ত্রণ করুন।</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.product-display.update') }}" class="space-y-6">
        @csrf

        {{-- Layout --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-table-cells-large"></i></span><div><h2 class="font-black">1. Layout & Responsive</h2><p class="text-xs text-slate-500">কোন screen-এ কতটি product থাকবে এবং grid/list কেমন হবে।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label><span class="mb-1.5 block text-sm font-bold">Layout</span><select name="layout" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="grid" @selected($settings['layout'] === 'grid')>Grid</option><option value="list" @selected($settings['layout'] === 'list')>List</option></select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Mobile Columns</span><select name="mobile_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([1,2] as $v)<option value="{{ $v }}" @selected((int)$settings['mobile_columns']===$v)>{{ $v }} Column</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Tablet Columns</span><select name="tablet_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([2,3,4] as $v)<option value="{{ $v }}" @selected((int)$settings['tablet_columns']===$v)>{{ $v }} Columns</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Desktop Columns</span><select name="desktop_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([3,4,5,6] as $v)<option value="{{ $v }}" @selected((int)$settings['desktop_columns']===$v)>{{ $v }} Columns</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Grid Gap</span><select name="gap" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([3,4,5,6,8] as $v)<option value="{{ $v }}" @selected((int)$settings['gap']===$v)>{{ $v }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Card Padding</span><select name="card_padding" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([3,4,5,6] as $v)<option value="{{ $v }}" @selected((int)$settings['card_padding']===$v)>{{ $v }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Text Alignment</span><select name="text_align" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="left" @selected($settings['text_align']==='left')>Left</option><option value="center" @selected($settings['text_align']==='center')>Center</option></select></label>
            </div>
        </section>

        {{-- Card --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-50 text-sky-600"><i class="fa-solid fa-credit-card"></i></span><div><h2 class="font-black">2. Card Style</h2><p class="text-xs text-slate-500">Border, radius, shadow এবং hover effect।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label><span class="mb-1.5 block text-sm font-bold">Card Style</span><select name="card_style" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="clean" @selected($settings['card_style']==='clean')>Clean</option><option value="bordered" @selected($settings['card_style']==='bordered')>Bold Border</option><option value="soft" @selected($settings['card_style']==='soft')>Soft</option><option value="glass" @selected($settings['card_style']==='glass')>Glass</option></select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Border Radius</span><select name="card_radius" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach(['none'=>'Square','lg'=>'Small','xl'=>'Medium','2xl'=>'Large','3xl'=>'Extra Large'] as $v=>$label)<option value="{{ $v }}" @selected($settings['card_radius']===$v)>{{ $label }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Shadow</span><select name="card_shadow" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach(['none','sm','md','lg','xl'] as $v)<option value="{{ $v }}" @selected($settings['card_shadow']===$v)>{{ strtoupper($v) }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Hover Effect</span><select name="hover_effect" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="none" @selected($settings['hover_effect']==='none')>None</option><option value="lift" @selected($settings['hover_effect']==='lift')>Lift</option><option value="zoom" @selected($settings['hover_effect']==='zoom')>Zoom</option><option value="lift-shadow" @selected($settings['hover_effect']==='lift-shadow')>Lift + Shadow</option></select></label>
            </div>
            @foreach(['card_border'=>'Show Card Border','image_zoom'=>'Image Hover Zoom'] as $key=>$label)
                <label class="mt-4 flex items-center justify-between rounded-2xl border border-slate-200 p-4"><span class="font-bold">{{ $label }}</span><span><input type="hidden" name="{{ $key }}" value="0"><input type="checkbox" name="{{ $key }}" value="1" @checked($settings[$key] ?? false) class="h-5 w-5 rounded border-slate-300 text-indigo-600"></span></label>
            @endforeach
        </section>

        {{-- Image --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-image"></i></span><div><h2 class="font-black">3. Product Image</h2><p class="text-xs text-slate-500">Image ratio, fit এবং background।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label><span class="mb-1.5 block text-sm font-bold">Image Ratio</span><select name="image_ratio" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach(['1/1'=>'Square 1:1','4/3'=>'Landscape 4:3','3/4'=>'Portrait 3:4','16/9'=>'Wide 16:9','auto'=>'Auto'] as $v=>$label)<option value="{{ $v }}" @selected($settings['image_ratio']===$v)>{{ $label }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Image Fit</span><select name="image_fit" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="cover" @selected($settings['image_fit']==='cover')>Cover</option><option value="contain" @selected($settings['image_fit']==='contain')>Contain</option></select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Image Background</span><select name="image_background" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="slate" @selected($settings['image_background']==='slate')>Light Gray</option><option value="white" @selected($settings['image_background']==='white')>White</option><option value="transparent" @selected($settings['image_background']==='transparent')>Transparent</option></select></label>
            </div>
        </section>

        {{-- Information --}}
        @php
            $toggles = [
                'show_image'=>'Product Image','show_category'=>'Category','show_brand'=>'Brand','show_sku'=>'SKU','show_description'=>'Short Description','show_price'=>'Price','show_regular_price'=>'Regular Price','show_rating'=>'Rating','show_stock'=>'Stock Status','show_stock_quantity'=>'Stock Quantity','show_discount_badge'=>'Discount Badge','show_new_badge'=>'NEW Badge','show_featured_badge'=>'Featured Badge','show_wishlist'=>'Wishlist','show_view_button'=>'View Product','show_add_to_cart'=>'Add to Cart',
            ];
        @endphp
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-list-check"></i></span><div><h2 class="font-black">4. What Will Be Shown</h2><p class="text-xs text-slate-500">Product card-এ কোন তথ্য/element দেখাবেন তা checkbox দিয়ে ঠিক করুন।</p></div></div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($toggles as $key=>$label)
                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/30"><span class="font-bold text-slate-700">{{ $label }}</span><span><input type="hidden" name="{{ $key }}" value="0"><input type="checkbox" name="{{ $key }}" value="1" @checked($settings[$key] ?? false) class="h-5 w-5 rounded border-slate-300 text-indigo-600"></span></label>
                @endforeach
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <label><span class="mb-1.5 block text-sm font-bold">Product Name Lines</span><select name="name_lines" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([1,2,3] as $v)<option value="{{ $v }}" @selected((int)$settings['name_lines']===$v)>{{ $v }} Line</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Description Lines</span><select name="description_lines" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([1,2,3] as $v)<option value="{{ $v }}" @selected((int)$settings['description_lines']===$v)>{{ $v }} Lines</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Price Size</span><select name="price_size" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach(['sm'=>'Small','lg'=>'Large','xl'=>'Extra Large','2xl'=>'2X Large'] as $v=>$label)<option value="{{ $v }}" @selected($settings['price_size']===$v)>{{ $label }}</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Price Decimals</span><select name="price_decimals" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([0,1,2] as $v)<option value="{{ $v }}" @selected((int)$settings['price_decimals']===$v)>{{ $v }} decimal</option>@endforeach</select></label>
                <label><span class="mb-1.5 block text-sm font-bold">Currency Position</span><select name="currency_position" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="before" @selected($settings['currency_position']==='before')>Before price</option><option value="after" @selected($settings['currency_position']==='after')>After price</option></select></label>
            </div>
        </section>

        {{-- Buttons --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-50 text-violet-600"><i class="fa-solid fa-hand-pointer"></i></span><div><h2 class="font-black">5. Button Style</h2><p class="text-xs text-slate-500">Action button-এর appearance এবং width।</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label><span class="mb-1.5 block text-sm font-bold">Button Style</span><select name="button_style" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="solid" @selected($settings['button_style']==='solid')>Solid</option><option value="outline" @selected($settings['button_style']==='outline')>Outline</option><option value="soft" @selected($settings['button_style']==='soft')>Soft</option></select></label>
                <label class="flex items-center justify-between rounded-2xl border border-slate-200 p-4"><span class="font-bold">Full Width Buttons</span><span><input type="hidden" name="button_full_width" value="0"><input type="checkbox" name="button_full_width" value="1" @checked($settings['button_full_width'] ?? false) class="h-5 w-5 rounded border-slate-300 text-indigo-600"></span></label>
                <label><span class="mb-1.5 block text-sm font-bold">Button Layout</span><select name="button_layout" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="row" @selected($settings['button_layout']==='row')>Inline / Row</option><option value="stack" @selected($settings['button_layout']==='stack')>Stacked</option></select></label>
            </div>
        </section>

        {{-- Placement --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-rose-50 text-rose-600"><i class="fa-solid fa-location-dot"></i></span><div><h2 class="font-black">6. কোথায় Product দেখাবে</h2><p class="text-xs text-slate-500">একই product card design Home এবং Shop উভয় জায়গায় ব্যবহার হবে।</p></div></div>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach(['show_home_products'=>'Home Page Product Section','show_shop_products'=>'Shop Page Product Section'] as $key=>$label)
                    <label class="flex items-center justify-between rounded-2xl border border-slate-200 p-4"><span class="font-bold">{{ $label }}</span><span><input type="hidden" name="{{ $key }}" value="0"><input type="checkbox" name="{{ $key }}" value="1" @checked($settings[$key] ?? false) class="h-5 w-5 rounded border-slate-300 text-indigo-600"></span></label>
                @endforeach
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <label class="sm:col-span-2"><span class="mb-1.5 block text-sm font-bold">Home Product Section Title</span><input name="home_title" value="{{ old('home_title', $settings['home_title']) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label><span class="mb-1.5 block text-sm font-bold">Home Product Limit</span><input type="number" min="1" max="24" name="home_limit" value="{{ old('home_limit', $settings['home_limit']) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label>
                <label><span class="mb-1.5 block text-sm font-bold">Home Desktop Columns</span><select name="home_desktop_columns" class="w-full rounded-xl border border-slate-200 px-4 py-3">@foreach([2,3,4,5,6] as $v)<option value="{{ $v }}" @selected((int)$settings['home_desktop_columns']===$v)>{{ $v }} Columns</option>@endforeach</select></label>
            </div>
        </section>

        <div class="sticky bottom-3 z-10 flex justify-end rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-xl backdrop-blur">
            <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-7 py-3 text-sm font-black text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5">Save All Product Display Settings</button>
        </div>
    </form>
</div>
@endsection
