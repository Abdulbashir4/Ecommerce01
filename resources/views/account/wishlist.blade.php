@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')
        <div class="min-w-0">
            <div class="mb-6"><p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Saved products</p><h1 class="mt-1 text-3xl font-black text-slate-950">My Wishlist</h1><p class="mt-2 text-sm text-slate-500">Keep products you want to revisit later.</p></div>

            @if($items->count())
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($items as $item)
                        @php($product = $item->product)
                        @if($product)
                            @php($image = $product->thumbnail ?: $product->featured_image)
                            @php($imageUrl = $image ? asset(str_starts_with($image, 'uploads/') ? $image : 'uploads/products/'.$image) : null)
                            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <a href="{{ route('product.show', $product->slug ?: $product->product_id) }}" class="block aspect-[4/3] bg-slate-100">
                                    @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" loading="lazy" class="h-full w-full object-contain" onerror="this.style.display='none';">@else<div class="grid h-full place-items-center text-slate-400"><i class="fa-solid fa-image text-3xl"></i></div>@endif
                                </a>
                                <div class="p-5">
                                    <a href="{{ route('product.show', $product->slug ?: $product->product_id) }}" class="line-clamp-2 font-black text-slate-900 hover:text-indigo-600">{{ $product->product_name }}</a>
                                    <p class="mt-2 text-lg font-black text-indigo-700">৳ {{ number_format($product->sale_price, 2) }}</p>
                                    <div class="mt-4 flex gap-2">
                                        <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-1">@csrf<input type="hidden" name="qty" value="1"><button class="w-full rounded-xl bg-slate-950 px-3 py-2.5 text-xs font-black text-white hover:bg-indigo-700"><i class="fa-solid fa-cart-plus mr-1"></i>Add to cart</button></form>
                                        <form method="POST" action="{{ route('account.wishlist.remove', $item) }}">@csrf @method('DELETE')<button title="Remove" class="grid h-10 w-10 place-items-center rounded-xl border border-red-100 text-red-600 hover:bg-red-50"><i class="fa-solid fa-heart-crack"></i></button></form>
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
                <div class="mt-6">{{ $items->links() }}</div>
            @else
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-rose-50 text-rose-400"><i class="fa-regular fa-heart text-2xl"></i></span>
                    <h2 class="mt-4 text-lg font-black">Your wishlist is empty</h2>
                    <p class="mt-1 text-sm text-slate-500">Save products you like so you can find them quickly later.</p>
                    <a href="{{ route('shop') }}" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white">Browse products</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
