@php
    $s = $settings;
    $currencySymbol = \App\Models\Setting::get('general.currency_symbol', '৳');
    $decimals = (int) ($s['price_decimals'] ?? 2);
    $currencyPosition = $s['currency_position'] ?? 'before';
    $productUrl = route('product.show', $product->slug ?: $product->product_id);
    $image = $product->thumbnail ?: $product->featured_image;
    $imageUrl = $image ? asset(str_starts_with($image, 'uploads/') ? $image : 'uploads/products/' . $image) : null;

    $regularPrice = (float) ($product->price ?? 0);
    $discountAmount = (float) ($product->discount_price ?? 0);
    $salePrice = max(0, $regularPrice - $discountAmount);
    if ($discountAmount <= 0) {
        $salePrice = $regularPrice;
    }
    $discount = $regularPrice > 0 && $discountAmount > 0 ? round(($discountAmount / $regularPrice) * 100) : (int) ($product->discount_percent ?? 0);

    $radius = match($s['card_radius']) {
        'none' => 'rounded-none', 'lg' => 'rounded-lg', 'xl' => 'rounded-xl', '3xl' => 'rounded-3xl', default => 'rounded-2xl'
    };
    $shadow = match($s['card_shadow']) {
        'none' => 'shadow-none', 'md' => 'shadow-md', 'lg' => 'shadow-lg', 'xl' => 'shadow-xl', default => 'shadow-sm'
    };
    $padding = match((int) $s['card_padding']) { 3 => 'p-3', 5 => 'p-5', 6 => 'p-6', default => 'p-4' };
    $isList = $s['layout'] === 'list';
    $textAlign = $s['text_align'] === 'center' ? 'text-center' : 'text-left';
    $ratio = match($s['image_ratio']) {
        '4/3' => 'aspect-[4/3]', '3/4' => 'aspect-[3/4]', '16/9' => 'aspect-video', 'auto' => '', default => 'aspect-square'
    };
    $fit = $s['image_fit'] === 'contain' ? 'object-contain' : 'object-cover';
    $imageBg = match($s['image_background']) {
        'white' => 'bg-white', 'transparent' => 'bg-transparent', default => 'bg-slate-100'
    };
    $hover = match($s['hover_effect']) {
        'lift' => 'hover:-translate-y-1', 'zoom' => 'hover:scale-[1.01]', 'lift-shadow' => 'hover:-translate-y-1 hover:shadow-xl', default => ''
    };
    $border = $s['card_border'] ? 'border border-slate-200' : 'border border-transparent';
    $style = match($s['card_style']) {
        'bordered' => 'bg-white border-2 border-slate-200',
        'soft' => 'bg-slate-50 border border-slate-100',
        'glass' => 'bg-white/90 border border-white/70 backdrop-blur',
        default => 'bg-white',
    };
    $nameLines = match((int) $s['name_lines']) { 1 => 'line-clamp-1', 3 => 'line-clamp-3', default => 'line-clamp-2' };
    $descriptionLines = match((int) $s['description_lines']) { 1 => 'line-clamp-1', 3 => 'line-clamp-3', default => 'line-clamp-2' };
    $priceSize = match($s['price_size']) { 'sm' => 'text-base', 'lg' => 'text-lg', '2xl' => 'text-2xl', default => 'text-xl' };
    $button = match($s['button_style']) {
        'outline' => 'border border-slate-300 bg-white text-slate-800 hover:border-indigo-500 hover:text-indigo-600',
        'soft' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
        default => 'bg-indigo-600 text-white hover:bg-indigo-700',
    };
@endphp

<article class="group relative flex min-w-0 flex-col overflow-hidden {{ $isList ? 'sm:flex-row' : '' }} {{ $radius }} {{ $shadow }} {{ $border }} {{ $style }} transition duration-300 {{ $hover }}">
    @if($s['show_image'])
        <div class="relative {{ $isList ? 'sm:h-auto sm:w-64 sm:shrink-0' : $ratio }} overflow-hidden {{ $imageBg }}">
            <a href="{{ $productUrl }}" class="block h-full w-full {{ $isList ? 'sm:min-h-56' : '' }}">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" loading="lazy" class="h-full w-full {{ $fit }} {{ $s['image_zoom'] ? 'transition duration-500 group-hover:scale-105' : '' }}" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                    <div class="{{ $imageUrl ? 'hidden ' : '' }}grid h-full place-items-center text-slate-400"><i class="fa-solid fa-image text-3xl"></i></div>
                @else
                    <div class="grid h-full place-items-center text-slate-400"><i class="fa-solid fa-image text-3xl"></i></div>
                @endif
            </a>

            @if($s['show_discount_badge'] && $discount > 0)
                <span class="absolute left-3 top-3 rounded-lg bg-red-500 px-2.5 py-1 text-xs font-black text-white shadow-md">-{{ $discount }}%</span>
            @endif
            @if($s['show_new_badge'] && !empty($product->is_new))
                <span class="absolute right-3 top-3 rounded-lg bg-emerald-500 px-2.5 py-1 text-xs font-black text-white shadow-md">NEW</span>
            @elseif($s['show_featured_badge'] && !empty($product->is_featured))
                <span class="absolute right-3 top-3 rounded-lg bg-amber-400 px-2.5 py-1 text-xs font-black text-slate-900 shadow-md">FEATURED</span>
            @endif

            @if($s['show_wishlist'])
                @auth
                    <form method="POST" action="{{ route('account.wishlist.add', $product->product_id) }}" class="absolute right-3 top-3">
                        @csrf
                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-full bg-white/95 text-slate-600 shadow-md transition hover:bg-indigo-600 hover:text-white" title="Add to wishlist">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="absolute right-3 top-3 grid h-9 w-9 place-items-center rounded-full bg-white/95 text-slate-600 shadow-md transition hover:bg-indigo-600 hover:text-white" title="Login to save">
                        <i class="fa-regular fa-heart"></i>
                    </a>
                @endauth
            @endif

            @if($s['show_view_button'])
                <a href="{{ $productUrl }}" class="absolute bottom-3 left-1/2 -translate-x-1/2 translate-y-3 rounded-xl bg-white/95 px-4 py-2 text-xs font-bold text-slate-800 opacity-0 shadow-lg backdrop-blur transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                    <i class="fa-regular fa-eye mr-1"></i> View Product
                </a>
            @endif
        </div>
    @endif

    <div class="flex flex-1 flex-col {{ $padding }} {{ $textAlign }}">
        @if($s['show_category'] && $product->category?->category_name)
            <p class="mb-1 text-[11px] font-black uppercase tracking-wider text-indigo-600">{{ $product->category->category_name }}</p>
        @endif

        @if($s['show_brand'] && $product->brand?->brand_name)
            <p class="mb-1 text-xs font-semibold text-slate-400">{{ $product->brand->brand_name }}</p>
        @endif

        <a href="{{ $productUrl }}" class="block">
            <h3 class="{{ $nameLines }} min-h-[24px] text-[15px] font-black leading-6 text-slate-800 transition group-hover:text-indigo-600">{{ $product->product_name }}</h3>
        </a>

        @if($s['show_sku'] && $product->sku)
            <p class="mt-1 text-xs text-slate-400">SKU: {{ $product->sku }}</p>
        @endif

        @if($s['show_description'] && $product->short_description)
            <p class="{{ $descriptionLines }} mt-2 text-sm leading-5 text-slate-500">{{ $product->short_description }}</p>
        @endif

        @if($s['show_rating'])
            <div class="mt-2 flex items-center gap-1 {{ $s['text_align'] === 'center' ? 'justify-center' : '' }} text-amber-400 text-xs">
                <span>★★★★★</span><span class="text-slate-400">(0)</span>
            </div>
        @endif

        @if($s['show_price'])
            <div class="mt-3 flex items-baseline gap-2 {{ $s['text_align'] === 'center' ? 'justify-center' : '' }}">
                <span class="{{ $priceSize }} font-black text-slate-900">
                    @if($currencyPosition === 'after'){{ number_format($salePrice, $decimals) }} {{ $currencySymbol }}@else{{ $currencySymbol }} {{ number_format($salePrice, $decimals) }}@endif
                </span>
                @if(($s['show_regular_price'] ?? true) && $discountAmount > 0 && $regularPrice > $salePrice)
                    <span class="text-sm text-slate-400 line-through">@if($currencyPosition === 'after'){{ number_format($regularPrice, $decimals) }} {{ $currencySymbol }}@else{{ $currencySymbol }} {{ number_format($regularPrice, $decimals) }}@endif</span>
                @endif
            </div>
        @endif

        @if($s['show_stock'])
            <p class="mt-2 text-xs font-bold {{ (($product->stock_qty ?? 0) > 0) ? 'text-emerald-600' : 'text-red-600' }}">
                {{ (($product->stock_qty ?? 0) > 0) ? 'In Stock' : 'Out of Stock' }}
                @if($s['show_stock_quantity'] ?? false)
                    <span class="ml-1 text-slate-400">({{ (int) ($product->stock_qty ?? 0) }})</span>
                @endif
            </p>
        @endif

        @if($s['show_view_button'] || $s['show_add_to_cart'])
            <div class="mt-4 flex gap-2 {{ ($s['button_layout'] ?? 'row') === 'stack' ? 'flex-col' : ($s['button_full_width'] ? 'flex-col sm:flex-row' : '') }}">
                @if($s['show_view_button'])
                    <a href="{{ $productUrl }}" class="flex flex-1 items-center justify-center rounded-xl px-3 py-2.5 text-sm font-bold transition {{ $button }}">
                        <i class="fa-regular fa-eye mr-2"></i> View Product
                    </a>
                @endif
                @if($s['show_add_to_cart'])
                    <form method="POST" action="{{ route('cart.add', $product->product_id) }}" class="{{ $s['button_full_width'] ? 'flex-1' : '' }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-slate-950 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">
                            <i class="fa-solid fa-cart-plus mr-2"></i> Add to Cart
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</article>
