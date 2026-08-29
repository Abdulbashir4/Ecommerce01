<?php $__env->startSection('content'); ?>

<div class="mx-auto max-w-7xl space-y-5">

    
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
            href="<?php echo e(url('/admin/products/create')); ?>"
            class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5"
        >
            <i class="fa-solid fa-plus mr-2"></i>
            Add Product
        </a>
    </div>


    
    <form
        method="GET"
        class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_180px_auto]"
    >

        <input
            name="q"
            value="<?php echo e(request('q')); ?>"
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
                <?php if(request('status') === 'active'): echo 'selected'; endif; ?>
            >
                Active
            </option>

            <option
                value="inactive"
                <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

                        <tr class="transition hover:bg-indigo-50/30">

                            
                            <td class="p-4">

                                <div class="flex items-center gap-3">

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->thumbnail): ?>

                                        <?php
                                            // Database may contain either:
                                            // 1) uploads/products/filename.jpg
                                            // 2) filename.jpg
                                            // Normalize both forms to one public URL.
                                            $thumbnailPath = ltrim($p->thumbnail, '/');
                                            if (!\Illuminate\Support\Str::startsWith($thumbnailPath, 'uploads/products/')) {
                                                $thumbnailPath = 'uploads/products/' . $thumbnailPath;
                                            }
                                        ?>

                                        <img
                                            src="<?php echo e(asset($thumbnailPath)); ?>"
                                            alt="<?php echo e($p->product_name); ?>"
                                            class="h-12 w-12 rounded-xl object-cover ring-1 ring-slate-200"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                                        >

                                        
                                        <span
                                            class="hidden h-12 w-12 place-items-center rounded-xl bg-slate-100 text-slate-400"
                                        >
                                            <i class="fa-solid fa-image"></i>
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="grid h-12 w-12 place-items-center rounded-xl bg-slate-100 text-slate-400"
                                        >
                                            <i class="fa-solid fa-image"></i>
                                        </span>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                                    <div>

                                        <div class="font-black text-slate-900">
                                            <?php echo e($p->product_name); ?>

                                        </div>

                                        <div class="text-xs text-slate-500">
                                            #<?php echo e($p->product_id); ?>


                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->sku): ?>
                                                · <?php echo e($p->sku); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                    </div>

                                </div>

                            </td>


                            
                            <td class="p-4">

                                <div class="font-semibold">
                                    <?php echo e($p->category?->category_name ?? '—'); ?>

                                </div>

                                <div class="text-xs text-slate-500">
                                    <?php echo e($p->brand?->brand_name ?? 'No brand'); ?>

                                </div>

                            </td>


                            
                            <td class="p-4 font-black">
                                ৳ <?php echo e(number_format($p->sale_price, 2)); ?>

                            </td>


                            
                            <td class="p-4 whitespace-nowrap">
                                <span class="font-bold">
                                    <?php echo e(number_format($p->stock_qty ?? 0)); ?>

                                </span>
                                <span class="ml-1 text-xs text-slate-500">
                                    <?php echo e($p->stock_status); ?>

                                </span>
                            </td>


                            
                            <td class="p-4">

                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-bold
                                    <?php echo e($p->status
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-slate-100 text-slate-600'); ?>"
                                >
                                    <?php echo e($p->status ? 'Active' : 'Inactive'); ?>

                                </span>

                            </td>


                            
                            <td class="p-4">

                                <div class="flex gap-2">


                                    
                                    <a
                                        href="<?php echo e(url('/admin/products/' . $p->product_id . '/edit')); ?>"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-indigo-600 transition hover:bg-indigo-50"
                                    >
                                        Edit
                                    </a>


                                    
                                    <form
                                        method="POST"
                                        action="<?php echo e(url('/admin/products/' . $p->product_id)); ?>"
                                        onsubmit="return confirm('Delete this product?')"
                                    >

                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>

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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                        <tr>

                            <td
                                colspan="6"
                                class="p-12 text-center text-slate-500"
                            >
                                No products found.
                            </td>

                        </tr>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    
    <div>
        <?php echo e($products->links()); ?>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/admin/products/index.blade.php ENDPATH**/ ?>