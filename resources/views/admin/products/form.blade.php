@extends('layouts.admin')
@section('content')
<div class="mx-auto max-w-6xl space-y-5"><div><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Catalog</p><h1 class="text-3xl font-black">{{ $product->exists?'Edit Product':'Add Product' }}</h1><p class="mt-1 text-sm text-slate-500">All fields are saved directly to your store database.</p></div>
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists?url('/admin/products/'.$product->product_id):url('/admin/products') }}" class="space-y-6">@csrf @if($product->exists) @method('PUT') @endif
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Basic Information</h2><div class="mt-5 grid gap-4 md:grid-cols-2"><label class="md:col-span-2"><span class="mb-2 block text-sm font-bold">Product name *</span><input name="product_name" value="{{ old('product_name',$product->product_name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></label><label><span class="mb-2 block text-sm font-bold">Slug</span><input name="slug" value="{{ old('slug',$product->slug) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">SKU</span><input name="sku" value="{{ old('sku',$product->sku) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Category</span><select name="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select category</option>@foreach($categories as $c)<option value="{{ $c->category_id }}" @selected(old('category_id',$product->category_id)==$c->category_id)>{{ $c->category_name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-bold">Subcategory</span><select name="subcategory_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select subcategory</option>@foreach($subcategories as $s)<option value="{{ $s->subcategory_id }}" @selected(old('subcategory_id',$product->subcategory_id)==$s->subcategory_id)>{{ $s->subcategory_name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-bold">Brand</span><select name="brand_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select brand</option>@foreach($brands as $b)<option value="{{ $b->brand_id }}" @selected(old('brand_id',$product->brand_id)==$b->brand_id)>{{ $b->brand_name }}</option>@endforeach</select></label></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Pricing & Stock</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><label><span class="mb-2 block text-sm font-bold">Price</span><input name="price" type="number" step="0.01" value="{{ old('price',$product->price) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Discount Amount</span><input name="discount_price" type="number" step="0.01" value="{{ old('discount_price',$product->discount_price) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Discount %</span><input name="discount_percent" type="number" value="{{ old('discount_percent',$product->discount_percent) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Purchase price</span><input name="purchase_price" type="number" step="0.01" value="{{ old('purchase_price',$product->purchase_price) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Stock quantity</span><input name="stock_qty" type="number" min="0" value="{{ old('stock_qty',$product->stock_qty) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Stock status</span><select name="stock_status" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option @selected(old('stock_status',$product->stock_status)==='In Stock')>In Stock</option><option @selected(old('stock_status',$product->stock_status)==='Out of Stock')>Out of Stock</option></select></label><label><span class="mb-2 block text-sm font-bold">Min order qty</span><input name="min_order_qty" type="number" min="1" value="{{ old('min_order_qty',$product->min_order_qty) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Max order qty</span><input name="max_order_qty" type="number" min="1" value="{{ old('max_order_qty',$product->max_order_qty) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Images</h2><div class="mt-5 grid gap-4 md:grid-cols-2"><label><span class="mb-2 block text-sm font-bold">Thumbnail</span><input type="file" name="thumbnail" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3"></label><label><span class="mb-2 block text-sm font-bold">Featured image</span><input type="file" name="featured_image" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3"></label></div><div class="mt-5 grid gap-5 sm:grid-cols-2">
<div>
    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Thumbnail preview</p>
    <div class="flex items-center gap-4">
        <div class="h-24 w-24 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
            @if($product->thumbnail)
                <img
                    id="thumbnail-preview"
                    src="{{ asset(ltrim(str_starts_with($product->thumbnail, 'uploads/') ? $product->thumbnail : 'uploads/products/' . $product->thumbnail, '/')) }}"
                    class="h-full w-full object-cover"
                    alt="Thumbnail"
                    onerror="this.style.display='none'; document.getElementById('thumbnail-placeholder').style.display='grid';"
                >
            @endif
            <span id="thumbnail-placeholder" class="{{ $product->thumbnail ? 'hidden' : 'grid' }} h-full w-full place-items-center text-slate-400">
                <i class="fa-solid fa-image"></i>
            </span>
        </div>
    </div>
</div>

<div>
    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Featured image preview</p>
    <div class="flex items-center gap-4">
        <div class="h-24 w-24 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
            @if($product->featured_image)
                <img
                    id="featured-image-preview"
                    src="{{ asset(ltrim(str_starts_with($product->featured_image, 'uploads/') ? $product->featured_image : 'uploads/products/' . $product->featured_image, '/')) }}"
                    class="h-full w-full object-cover"
                    alt="Featured image"
                    onerror="this.style.display='none'; document.getElementById('featured-image-placeholder').style.display='grid';"
                >
            @endif
            <span id="featured-image-placeholder" class="{{ $product->featured_image ? 'hidden' : 'grid' }} h-full w-full place-items-center text-slate-400">
                <i class="fa-solid fa-image"></i>
            </span>
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
            } else {
                const img = document.createElement('img');
                img.id = previewId;
                img.className = 'h-full w-full object-cover';
                img.alt = inputId;
                img.src = url;

                const box = placeholder.parentElement;
                box.insertBefore(img, placeholder);
            }

            if (placeholder) {
                placeholder.style.display = 'none';
            }
        });
    }

    previewImage('thumbnail', 'thumbnail-preview', 'thumbnail-placeholder');
    previewImage('featured_image', 'featured-image-preview', 'featured-image-placeholder');
});
</script></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Descriptions</h2><div class="mt-5 grid gap-4"><textarea name="short_description" rows="3" placeholder="Short description" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('short_description',$product->short_description) }}</textarea><textarea name="long_description" rows="7" placeholder="Long description" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('long_description',$product->long_description) }}</textarea><textarea name="specifications" rows="5" placeholder="Specifications" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('specifications',$product->specifications) }}</textarea></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">@foreach(['status'=>'Active','is_featured'=>'Featured','is_trending'=>'Trending','is_new'=>'New','flash_sale'=>'Flash Sale'] as $field=>$label)<label class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 text-sm font-bold"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field,$product->exists?$product->{$field}:($field==='status'))) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">{{ $label }}</label>@endforeach</div></section>
<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="{{ url('/admin/products') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-center font-bold text-slate-700">Cancel</a><button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-7 py-3 font-black text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5">{{ $product->exists?'Update Product':'Save Product' }}</button></div>
</form></div>
@endsection
