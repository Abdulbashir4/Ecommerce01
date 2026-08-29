<div class="space-y-5">

    <input
        wire:model.live.debounce.300ms="search"
        class="w-full rounded-xl border px-4 py-3"
        placeholder="Search products..."
    >

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        @foreach($products as $p)

            <div class="overflow-hidden rounded-2xl border bg-white shadow-sm">

                <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                    @if($p->thumbnail)
                        <img
                            src="{{ asset('uploads/products/' . $p->thumbnail) }}"
                            alt="{{ $p->product_name }}"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                    @else
                        <span class="text-slate-400">No image</span>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="font-semibold">
                        {{ $p->product_name }}
                    </h3>

                    <p class="mt-2 text-lg font-bold">
                        ৳ {{ number_format($p->sale_price, 2) }}
                    </p>

                    <a
                        class="mt-3 inline-block text-blue-600"
                        href="{{ route('product.show', $p->slug ?: $p->product_id) }}"
                    >
                        View
                    </a>
                </div>

            </div>

        @endforeach

    </div>

    <div>
        {{ $products->links() }}
    </div>

</div>