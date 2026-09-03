<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        <?php echo $__env->make('account.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="min-w-0">
            <div class="mb-6"><p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Your feedback</p><h1 class="mt-1 text-3xl font-black text-slate-950">My Reviews</h1><p class="mt-2 text-sm text-slate-500">Review products you have purchased and see your submitted feedback.</p></div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reviewableItems->isNotEmpty()): ?>
                <section class="mb-6 rounded-3xl border border-indigo-100 bg-indigo-50/60 p-5 sm:p-6">
                    <h2 class="text-lg font-black text-slate-950">Products waiting for your review</h2>
                    <div class="mt-4 grid gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $reviewableItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php ($product = $entry['item']->product); ?>
                            <div class="rounded-2xl border border-white bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="truncate font-black text-slate-900"><?php echo e($product->product_name); ?></p>
                                        <p class="mt-1 text-xs text-slate-500">Order #<?php echo e($entry['order']->order_id); ?></p>
                                    </div>
                                    <details class="shrink-0">
                                        <summary class="cursor-pointer list-none rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-xs font-black text-white hover:bg-indigo-700">Write review</summary>
                                        <form method="POST" action="<?php echo e(route('account.reviews.store')); ?>" class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 sm:min-w-[26rem]">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="order_id" value="<?php echo e($entry['order']->order_id); ?>">
                                            <input type="hidden" name="product_id" value="<?php echo e($product->product_id); ?>">
                                            <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Rating</span><select name="rating" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"><option value="">Choose rating</option><option value="5">★★★★★ Excellent</option><option value="4">★★★★☆ Good</option><option value="3">★★★☆☆ Average</option><option value="2">★★☆☆☆ Poor</option><option value="1">★☆☆☆☆ Very poor</option></select></label>
                                            <label class="mt-3 block"><span class="mb-1.5 block text-xs font-black text-slate-600">Review</span><textarea name="review" rows="3" maxlength="2000" placeholder="Tell us about the product..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></textarea></label>
                                            <button class="mt-3 w-full rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-black text-white hover:bg-indigo-700">Submit review</button>
                                        </form>
                                    </details>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 p-5 sm:p-6"><h2 class="text-xl font-black">Submitted Reviews</h2></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="border-b border-slate-100 p-5 last:border-b-0 sm:p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div><p class="font-black text-slate-900"><?php echo e($review->product?->product_name ?: 'Product'); ?></p><p class="mt-1 text-xs text-slate-500">Order #<?php echo e($review->order_id); ?> · <?php echo e($review->created_at?->format('d M Y')); ?></p></div>
                            <span class="tracking-widest text-amber-500"><?php echo e(str_repeat('★', $review->rating)); ?><span class="text-slate-200"><?php echo e(str_repeat('★', 5 - $review->rating)); ?></span></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->review): ?><p class="mt-3 text-sm leading-6 text-slate-600"><?php echo e($review->review); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="p-10 text-center"><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-amber-50 text-amber-400"><i class="fa-regular fa-star text-xl"></i></span><h2 class="mt-4 font-black">No reviews yet</h2><p class="mt-1 text-sm text-slate-500">Once you purchase a product, you can review it here.</p></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reviews->hasPages()): ?><div class="mt-5"><?php echo e($reviews->links()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/account/reviews.blade.php ENDPATH**/ ?>