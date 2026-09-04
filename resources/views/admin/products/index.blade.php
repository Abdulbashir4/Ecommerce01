@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-7xl space-y-5">

    {{-- Header --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">
                Catalog
            </p>

            <h1 class="text-3xl font-black">
                Product List
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Manage products, stock and visibility.
            </p>
        </div>

        <a
            href="{{ url('/admin/products/create') }}"
            class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5"
        >
            <i class="fa-solid fa-plus mr-2"></i>
            Add Product
        </a>

        @if(auth()->user()->hasPermission('products.edit'))
            <a href="{{ route('admin.products.image-editor') }}" class="rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-3 text-sm font-black text-indigo-700 transition hover:bg-indigo-100">
                <i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Image Editor
            </a>
        @endif

        @if(auth()->user()->hasPermission('products.delete'))
            <a
                href="{{ route('admin.products.image-cleanup') }}"
                class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-black text-amber-700 transition hover:bg-amber-100"
            >
                <i class="fa-solid fa-broom mr-2"></i>
                Cleanup Images
            </a>
        @endif
    </div>


    {{-- Search --}}
    <form
        method="GET"
        class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_180px_auto]"
    >

        <input
            name="q"
            value="{{ request('q') }}"
            placeholder="Search product name..."
            class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
        >

        <select
            name="status"
            class="rounded-xl border border-slate-200 px-4 py-3 text-sm"
        >
            <option value="">All status</option>

            <option
                value="active"
                @selected(request('status') === 'active')
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(request('status') === 'inactive')
            >
                Inactive
            </option>
        </select>

        <button
            type="submit"
            class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-indigo-700"
        >
            <i class="fa-solid fa-magnifying-glass mr-2"></i>
            Search
        </button>

    </form>


    {{-- Product Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">

                    <tr>
                        <th class="p-4">
                            Product
                        </th>

                        <th class="p-4">
                            Category
                        </th>

                        <th class="p-4">
                            Price
                        </th>

                        <th class="p-4">
                            Stock
                        </th>

                        <th class="p-4">
                            Status
                        </th>

                        <th class="p-4 text-right">
                            Action
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($products as $p)

                        <tr class="transition hover:bg-indigo-50/30">

                            {{-- Product --}}
                            <td class="p-4">

                                <div class="flex items-center gap-3">

                                    {{-- Product Image --}}
                                    @if($p->thumbnail)

                                        @php
                                            // Database may contain either:
                                            // 1) uploads/products/filename.jpg
                                            // 2) filename.jpg
                                            // Normalize both forms to one public URL.
                                            $thumbnailPath = ltrim($p->thumbnail, '/');
                                            if (!\Illuminate\Support\Str::startsWith($thumbnailPath, 'uploads/products/')) {
                                                $thumbnailPath = 'uploads/products/' . $thumbnailPath;
                                            }
                                        @endphp

                                        <img
                                            src="{{ asset($thumbnailPath) }}"
                                            alt="{{ $p->product_name }}"
                                            class="h-12 w-12 rounded-xl object-cover ring-1 ring-slate-200"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                                        >

                                        {{-- Image fallback --}}
                                        <span
                                            class="hidden h-12 w-12 place-items-center rounded-xl bg-slate-100 text-slate-400"
                                        >
                                            <i class="fa-solid fa-image"></i>
                                        </span>

                                    @else

                                        <span
                                            class="grid h-12 w-12 place-items-center rounded-xl bg-slate-100 text-slate-400"
                                        >
                                            <i class="fa-solid fa-image"></i>
                                        </span>

                                    @endif


                                    <div>

                                        <div class="font-black text-slate-900">
                                            {{ $p->product_name }}
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            #{{ $p->product_id }}

                                            @if($p->sku)
                                                · {{ $p->sku }}
                                            @endif
                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- Category --}}
                            <td class="p-4">

                                <div class="font-semibold">
                                    {{ $p->category?->category_name ?? '—' }}
                                </div>

                                <div class="text-xs text-slate-500">
                                    {{ $p->brand?->brand_name ?? 'No brand' }}
                                </div>

                            </td>


                            {{-- Price --}}
                            <td class="p-4 font-black">
                                ৳ {{ number_format($p->sale_price, 2) }}
                            </td>


                            {{-- Stock --}}
                            <td class="p-4 whitespace-nowrap">
                                <span class="font-bold">
                                    {{ number_format($p->stock_qty ?? 0) }}
                                </span>
                                <span class="ml-1 text-xs text-slate-500">
                                    {{ $p->stock_status }}
                                </span>
                            </td>


                            {{-- Status --}}
                            <td class="p-4">

                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-bold
                                    {{ $p->status
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-slate-100 text-slate-600'
                                    }}"
                                >
                                    {{ $p->status ? 'Active' : 'Inactive' }}
                                </span>

                            </td>


                            {{-- Actions --}}
                            <td class="p-4">

                                <div class="flex gap-2">


                                    {{-- Edit --}}
                                    <a
                                        href="{{ url('/admin/products/' . $p->product_id . '/edit') }}"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-indigo-600 transition hover:bg-indigo-50"
                                    >
                                        Edit
                                    </a>


                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ url('/admin/products/' . $p->product_id) }}"
                                        onsubmit="return confirm('Delete this product?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg border border-red-100 px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="p-12 text-center text-slate-500"
                            >
                                No products found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    <div>
        {{ $products->links() }}
    </div>

</div>

@endsection