@extends('layouts.admin')
@section('content')
<div class="mx-auto max-w-6xl space-y-5"><div><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Catalog</p><h1 class="text-3xl font-black">{{ $product->exists?'Edit Product':'Add Product' }}</h1><p class="mt-1 text-sm text-slate-500">All fields are saved directly to your store database.</p></div>
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists?url('/admin/products/'.$product->product_id):url('/admin/products') }}" class="space-y-6">@csrf @if($product->exists) @method('PUT') @endif
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Basic Information</h2><div class="mt-5 grid gap-4 md:grid-cols-2"><label class="md:col-span-2"><span class="mb-2 block text-sm font-bold">Product name *</span><input name="product_name" value="{{ old('product_name',$product->product_name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></label><label><span class="mb-2 block text-sm font-bold">Slug</span><input name="slug" value="{{ old('slug',$product->slug) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">SKU</span><input name="sku" value="{{ old('sku',$product->sku) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Category</span><select name="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select category</option>@foreach($categories as $c)<option value="{{ $c->category_id }}" @selected(old('category_id',$product->category_id)==$c->category_id)>{{ $c->category_name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-bold">Subcategory</span><select name="subcategory_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select subcategory</option>@foreach($subcategories as $s)<option value="{{ $s->subcategory_id }}" @selected(old('subcategory_id',$product->subcategory_id)==$s->subcategory_id)>{{ $s->subcategory_name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-bold">Brand</span><select name="brand_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select brand</option>@foreach($brands as $b)<option value="{{ $b->brand_id }}" @selected(old('brand_id',$product->brand_id)==$b->brand_id)>{{ $b->brand_name }}</option>@endforeach</select></label></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Pricing & Stock</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><label><span class="mb-2 block text-sm font-bold">Price</span><input name="price" type="number" step="0.01" value="{{ old('price',$product->price) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Discount Amount</span><input name="discount_price" type="number" step="0.01" value="{{ old('discount_price',$product->discount_price) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Discount %</span><input name="discount_percent" type="number" value="{{ old('discount_percent',$product->discount_percent) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Purchase price</span><input name="purchase_price" type="number" step="0.01" value="{{ old('purchase_price',$product->purchase_price) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Stock quantity</span><input name="stock_qty" type="number" min="0" value="{{ old('stock_qty',$product->stock_qty) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Stock status</span><select name="stock_status" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option @selected(old('stock_status',$product->stock_status)==='In Stock')>In Stock</option><option @selected(old('stock_status',$product->stock_status)==='Out of Stock')>Out of Stock</option></select></label><label><span class="mb-2 block text-sm font-bold">Min order qty</span><input name="min_order_qty" type="number" min="1" value="{{ old('min_order_qty',$product->min_order_qty) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Max order qty</span><input name="max_order_qty" type="number" min="1" value="{{ old('max_order_qty',$product->max_order_qty) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-black">Images</h2>
            <p class="mt-1 text-sm text-slate-500">Add as many gallery images as you need. There is no application-level 5-image limit.</p>
        </div>
        @if($product->exists)
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-black text-indigo-700">
                {{ count((array) ($product->gallery_images ?? [])) }} existing gallery image(s)
            </span>
        @endif
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-2">
        <label>
            <span class="mb-2 block text-sm font-bold">Thumbnail</span>
            <input type="file" name="thumbnail" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3">
        </label>
        <label>
            <span class="mb-2 block text-sm font-bold">Featured image</span>
            <input type="file" name="featured_image" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3">
        </label>
    </div>

    <div class="mt-6 rounded-2xl border border-indigo-100 bg-indigo-50/40 p-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Gallery images</h3>
                <p class="mt-1 text-xs text-slate-500">Select multiple images at once, or add more upload fields below. All selected files will be saved to this product.</p>
            </div>
            <button type="button" id="add-gallery-input" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-indigo-700">
                <i class="fa-solid fa-plus"></i>
                Add another input
            </button>
        </div>

        <div id="gallery-inputs" class="mt-4 space-y-3">
            <div class="gallery-input-row flex flex-col gap-2 sm:flex-row sm:items-center">
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="gallery-file-input min-w-0 flex-1 rounded-xl border border-slate-200 bg-white p-3 text-sm">
                <button type="button" class="remove-gallery-input hidden shrink-0 rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                    Remove input
                </button>
            </div>
        </div>

        <div id="gallery-selected-files" class="mt-3 hidden rounded-xl bg-white p-3 text-xs text-slate-600"></div>
    </div>

    @php
        $existingGallery = is_array($product->gallery_images ?? null)
            ? $product->gallery_images
            : (json_decode((string) ($product->gallery_images ?? ''), true) ?: []);
    @endphp

    @if($product->exists && count($existingGallery))
        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">Existing gallery</p>
                <p class="text-xs text-slate-400">Tick an image to remove it when you save.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach($existingGallery as $galleryIndex => $galleryImage)
                    @php
                        $galleryPath = is_string($galleryImage) ? $galleryImage : ($galleryImage['path'] ?? $galleryImage['url'] ?? $galleryImage['image'] ?? '');
                        $galleryUrl = preg_match('/^https?:\/\//i', (string) $galleryPath)
                            ? $galleryPath
                            : asset('uploads/products/' . ltrim((string) $galleryPath, '/'));
                    @endphp
                    @if($galleryPath)
                        <label class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
                            <div class="aspect-square overflow-hidden rounded-xl bg-slate-50">
                                <img src="{{ $galleryUrl }}" alt="Gallery image {{ $galleryIndex + 1 }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-105" loading="lazy">
                            </div>
                            <span class="mt-2 flex items-center gap-2 text-xs font-bold text-rose-600">
                                <input type="checkbox" name="remove_gallery_images[]" value="{{ $galleryPath }}" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                Remove
                            </span>
                        </label>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Thumbnail preview</p>
            <div class="flex items-center gap-4">
                <div class="h-24 w-24 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                    @if($product->thumbnail)
                        <img id="thumbnail-preview" src="{{ asset(ltrim(str_starts_with($product->thumbnail, 'uploads/') ? $product->thumbnail : 'uploads/products/' . $product->thumbnail, '/')) }}" class="h-full w-full object-cover" alt="Thumbnail" onerror="this.style.display='none'; document.getElementById('thumbnail-placeholder').style.display='grid';">
                    @endif
                    <span id="thumbnail-placeholder" class="{{ $product->thumbnail ? 'hidden' : 'grid' }} h-full w-full place-items-center text-slate-400"><i class="fa-solid fa-image"></i></span>
                </div>
            </div>
        </div>

        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Featured image preview</p>
            <div class="flex items-center gap-4">
                <div class="h-24 w-24 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                    @if($product->featured_image)
                        <img id="featured-image-preview" src="{{ asset(ltrim(str_starts_with($product->featured_image, 'uploads/') ? $product->featured_image : 'uploads/products/' . $product->featured_image, '/')) }}" class="h-full w-full object-cover" alt="Featured image" onerror="this.style.display='none'; document.getElementById('featured-image-placeholder').style.display='grid';">
                    @endif
                    <span id="featured-image-placeholder" class="{{ $product->featured_image ? 'hidden' : 'grid' }} h-full w-full place-items-center text-slate-400"><i class="fa-solid fa-image"></i></span>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function previewImage(inputId, previewId, placeholderId) {
            const input = document.querySelector('input[name="' + inputId + '"]');
            if (!input) return;

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;

                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                const url = URL.createObjectURL(file);

                if (preview) {
                    preview.src = url;
                    preview.style.display = 'block';
                } else if (placeholder) {
                    const img = document.createElement('img');
                    img.id = previewId;
                    img.className = 'h-full w-full object-cover';
                    img.alt = inputId;
                    img.src = url;
                    placeholder.parentElement.insertBefore(img, placeholder);
                }

                if (placeholder) placeholder.style.display = 'none';
            });
        }

        previewImage('thumbnail', 'thumbnail-preview', 'thumbnail-placeholder');
        previewImage('featured_image', 'featured-image-preview', 'featured-image-placeholder');

        const inputs = document.getElementById('gallery-inputs');
        const addButton = document.getElementById('add-gallery-input');
        const selectedBox = document.getElementById('gallery-selected-files');

        function updateSelectedFiles() {
            if (!selectedBox) return;
            const files = [];
            inputs.querySelectorAll('.gallery-file-input').forEach(function (input) {
                Array.from(input.files || []).forEach(function (file) {
                    files.push(file.name);
                });
            });

            if (!files.length) {
                selectedBox.classList.add('hidden');
                selectedBox.innerHTML = '';
                return;
            }

            selectedBox.classList.remove('hidden');
            selectedBox.innerHTML = '<strong>' + files.length + ' new gallery image' + (files.length === 1 ? '' : 's') + ' selected:</strong> ' + files.map(function (name) {
                return '<span class="ml-1 inline-block rounded-full bg-slate-100 px-2 py-1">' + escapeHtml(name) + '</span>';
            }).join(' ');
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }

        function bindGalleryInput(row) {
            const input = row.querySelector('.gallery-file-input');
            const remove = row.querySelector('.remove-gallery-input');

            if (input) input.addEventListener('change', updateSelectedFiles);

            if (remove) {
                remove.addEventListener('click', function () {
                    row.remove();
                    updateSelectedFiles();
                });
            }
        }

        bindGalleryInput(inputs.querySelector('.gallery-input-row'));

        if (addButton) {
            addButton.addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'gallery-input-row flex flex-col gap-2 sm:flex-row sm:items-center';
                row.innerHTML = '<input type="file" name="gallery_images[]" accept="image/*" multiple class="gallery-file-input min-w-0 flex-1 rounded-xl border border-slate-200 bg-white p-3 text-sm">' +
                    '<button type="button" class="remove-gallery-input shrink-0 rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">Remove input</button>';
                inputs.appendChild(row);
                bindGalleryInput(row);
            });
        }
    });
    </script>
</section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Descriptions</h2><div class="mt-5 grid gap-4"><textarea name="short_description" rows="3" placeholder="Short description" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('short_description',$product->short_description) }}</textarea><textarea name="long_description" rows="7" placeholder="Long description" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('long_description',$product->long_description) }}</textarea><textarea name="specifications" rows="5" placeholder="Specifications" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('specifications',$product->specifications) }}</textarea></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">@foreach(['status'=>'Active','is_featured'=>'Featured','is_trending'=>'Trending','is_new'=>'New','flash_sale'=>'Flash Sale'] as $field=>$label)<label class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 text-sm font-bold"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field,$product->exists?$product->{$field}:($field==='status'))) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">{{ $label }}</label>@endforeach</div></section>
<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="{{ url('/admin/products') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-center font-bold text-slate-700">Cancel</a><button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-7 py-3 font-black text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5">{{ $product->exists?'Update Product':'Save Product' }}</button></div>
</form></div>
@endsection
