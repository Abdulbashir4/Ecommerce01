@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        @include('account.partials.nav')
        <div class="min-w-0">
            <div class="mb-6"><p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Your feedback</p><h1 class="mt-1 text-3xl font-black text-slate-950">My Reviews</h1><p class="mt-2 text-sm text-slate-500">Review products you have purchased and see your submitted feedback.</p></div>

            @if($reviewableItems->isNotEmpty())
                <section class="mb-6 rounded-3xl border border-indigo-100 bg-indigo-50/60 p-5 sm:p-6">
                    <h2 class="text-lg font-black text-slate-950">Products waiting for your review</h2>
                    <div class="mt-4 grid gap-3">
                        @foreach($reviewableItems as $entry)
                            @php($product = $entry['item']->product)
                            <div class="rounded-2xl border border-white bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="truncate font-black text-slate-900">{{ $product->product_name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Order #{{ $entry['order']->order_id }}</p>
                                    </div>
                                    <details class="shrink-0">
                                        <summary class="cursor-pointer list-none rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-xs font-black text-white hover:bg-indigo-700">Write review</summary>
                                        <form method="POST" action="{{ route('account.reviews.store') }}" class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 sm:min-w-[26rem]">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $entry['order']->order_id }}">
                                            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                            <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Rating</span><select name="rating" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"><option value="">Choose rating</option><option value="5">★★★★★ Excellent</option><option value="4">★★★★☆ Good</option><option value="3">★★★☆☆ Average</option><option value="2">★★☆☆☆ Poor</option><option value="1">★☆☆☆☆ Very poor</option></select></label>
                                            <label class="mt-3 block"><span class="mb-1.5 block text-xs font-black text-slate-600">Review</span><textarea name="review" rows="3" maxlength="2000" placeholder="Tell us about the product..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></textarea></label>
                                            <button class="mt-3 w-full rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-black text-white hover:bg-indigo-700">Submit review</button>
                                        </form>
                                    </details>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 p-5 sm:p-6"><h2 class="text-xl font-black">Submitted Reviews</h2></div>
                @forelse($reviews as $review)
                    <div class="border-b border-slate-100 p-5 last:border-b-0 sm:p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div><p class="font-black text-slate-900">{{ $review->product?->product_name ?: 'Product' }}</p><p class="mt-1 text-xs text-slate-500">Order #{{ $review->order_id }} · {{ $review->created_at?->format('d M Y') }}</p></div>
                            <span class="tracking-widest text-amber-500">{{ str_repeat('★', $review->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                        </div>
                        @if($review->review)<p class="mt-3 text-sm leading-6 text-slate-600">{{ $review->review }}</p>@endif
                    </div>
                @empty
                    <div class="p-10 text-center"><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-amber-50 text-amber-400"><i class="fa-regular fa-star text-xl"></i></span><h2 class="mt-4 font-black">No reviews yet</h2><p class="mt-1 text-sm text-slate-500">Once you purchase a product, you can review it here.</p></div>
                @endforelse
            </section>
            @if($reviews->hasPages())<div class="mt-5">{{ $reviews->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
