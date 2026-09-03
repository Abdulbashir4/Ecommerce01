<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-6xl space-y-5"><div><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Catalog</p><h1 class="text-3xl font-black"><?php echo e($product->exists?'Edit Product':'Add Product'); ?></h1><p class="mt-1 text-sm text-slate-500">All fields are saved directly to your store database.</p></div>
<form method="POST" enctype="multipart/form-data" action="<?php echo e($product->exists?url('/admin/products/'.$product->product_id):url('/admin/products')); ?>" class="space-y-6"><?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Basic Information</h2><div class="mt-5 grid gap-4 md:grid-cols-2"><label class="md:col-span-2"><span class="mb-2 block text-sm font-bold">Product name *</span><input name="product_name" value="<?php echo e(old('product_name',$product->product_name)); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></label><label><span class="mb-2 block text-sm font-bold">Slug</span><input name="slug" value="<?php echo e(old('slug',$product->slug)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">SKU</span><input name="sku" value="<?php echo e(old('sku',$product->sku)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Category</span><select name="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select category</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($c->category_id); ?>" <?php if(old('category_id',$product->category_id)==$c->category_id): echo 'selected'; endif; ?>><?php echo e($c->category_name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label><label><span class="mb-2 block text-sm font-bold">Subcategory</span><select name="subcategory_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select subcategory</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($s->subcategory_id); ?>" <?php if(old('subcategory_id',$product->subcategory_id)==$s->subcategory_id): echo 'selected'; endif; ?>><?php echo e($s->subcategory_name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label><label><span class="mb-2 block text-sm font-bold">Brand</span><select name="brand_id" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option value="">Select brand</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($b->brand_id); ?>" <?php if(old('brand_id',$product->brand_id)==$b->brand_id): echo 'selected'; endif; ?>><?php echo e($b->brand_name); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Pricing & Stock</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><label><span class="mb-2 block text-sm font-bold">Price</span><input name="price" type="number" step="0.01" value="<?php echo e(old('price',$product->price)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Discount Amount</span><input name="discount_price" type="number" step="0.01" value="<?php echo e(old('discount_price',$product->discount_price)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Discount %</span><input name="discount_percent" type="number" value="<?php echo e(old('discount_percent',$product->discount_percent)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Purchase price</span><input name="purchase_price" type="number" step="0.01" value="<?php echo e(old('purchase_price',$product->purchase_price)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Stock quantity</span><input name="stock_qty" type="number" min="0" value="<?php echo e(old('stock_qty',$product->stock_qty)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Stock status</span><select name="stock_status" class="w-full rounded-xl border border-slate-200 px-4 py-3"><option <?php if(old('stock_status',$product->stock_status)==='In Stock'): echo 'selected'; endif; ?>>In Stock</option><option <?php if(old('stock_status',$product->stock_status)==='Out of Stock'): echo 'selected'; endif; ?>>Out of Stock</option></select></label><label><span class="mb-2 block text-sm font-bold">Min order qty</span><input name="min_order_qty" type="number" min="1" value="<?php echo e(old('min_order_qty',$product->min_order_qty)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label><label><span class="mb-2 block text-sm font-bold">Max order qty</span><input name="max_order_qty" type="number" min="1" value="<?php echo e(old('max_order_qty',$product->max_order_qty)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3"></label></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Images</h2><div class="mt-5 grid gap-4 md:grid-cols-2"><label><span class="mb-2 block text-sm font-bold">Thumbnail</span><input type="file" name="thumbnail" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3"></label><label><span class="mb-2 block text-sm font-bold">Featured image</span><input type="file" name="featured_image" accept="image/*" class="w-full rounded-xl border border-slate-200 p-3"></label></div><div class="mt-5 grid gap-5 sm:grid-cols-2">
<div>
    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Thumbnail preview</p>
    <div class="flex items-center gap-4">
        <div class="h-24 w-24 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->thumbnail): ?>
                <img
                    id="thumbnail-preview"
                    src="<?php echo e(asset(ltrim(str_starts_with($product->thumbnail, 'uploads/') ? $product->thumbnail : 'uploads/products/' . $product->thumbnail, '/'))); ?>"
                    class="h-full w-full object-cover"
                    alt="Thumbnail"
                    onerror="this.style.display='none'; document.getElementById('thumbnail-placeholder').style.display='grid';"
                >
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span id="thumbnail-placeholder" class="<?php echo e($product->thumbnail ? 'hidden' : 'grid'); ?> h-full w-full place-items-center text-slate-400">
                <i class="fa-solid fa-image"></i>
            </span>
        </div>
    </div>
</div>

<div>
    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">Featured image preview</p>
    <div class="flex items-center gap-4">
        <div class="h-24 w-24 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->featured_image): ?>
                <img
                    id="featured-image-preview"
                    src="<?php echo e(asset(ltrim(str_starts_with($product->featured_image, 'uploads/') ? $product->featured_image : 'uploads/products/' . $product->featured_image, '/'))); ?>"
                    class="h-full w-full object-cover"
                    alt="Featured image"
                    onerror="this.style.display='none'; document.getElementById('featured-image-placeholder').style.display='grid';"
                >
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span id="featured-image-placeholder" class="<?php echo e($product->featured_image ? 'hidden' : 'grid'); ?> h-full w-full place-items-center text-slate-400">
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
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black">Descriptions</h2><div class="mt-5 grid gap-4"><textarea name="short_description" rows="3" placeholder="Short description" class="w-full rounded-xl border border-slate-200 px-4 py-3"><?php echo e(old('short_description',$product->short_description)); ?></textarea><textarea name="long_description" rows="7" placeholder="Long description" class="w-full rounded-xl border border-slate-200 px-4 py-3"><?php echo e(old('long_description',$product->long_description)); ?></textarea><textarea name="specifications" rows="5" placeholder="Specifications" class="w-full rounded-xl border border-slate-200 px-4 py-3"><?php echo e(old('specifications',$product->specifications)); ?></textarea></div></section>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['status'=>'Active','is_featured'=>'Featured','is_trending'=>'Trending','is_new'=>'New','flash_sale'=>'Flash Sale']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><label class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 text-sm font-bold"><input type="checkbox" name="<?php echo e($field); ?>" value="1" <?php if(old($field,$product->exists?$product->{$field}:($field==='status'))): echo 'checked'; endif; ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"><?php echo e($label); ?></label><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div></section>
<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="<?php echo e(url('/admin/products')); ?>" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-center font-bold text-slate-700">Cancel</a><button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-7 py-3 font-black text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5"><?php echo e($product->exists?'Update Product':'Save Product'); ?></button></div>
</form></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/admin/products/form.blade.php ENDPATH**/ ?>