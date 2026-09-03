<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        <?php echo $__env->make('account.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="min-w-0">
            <div class="mb-6">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Purchase history</p>
                <h1 class="mt-1 text-3xl font-black text-slate-950">My Orders</h1>
                <p class="mt-2 text-sm text-slate-500">Review every order and open its full details.</p>
            </div>

            <div class="mb-5 flex gap-2 overflow-x-auto pb-1">
                <a href="<?php echo e(route('account.orders')); ?>" class="shrink-0 rounded-full px-4 py-2 text-xs font-black <?php echo e(!$activeStatus ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600'); ?>">All</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e(route('account.orders', ['status' => $status])); ?>" class="shrink-0 rounded-full px-4 py-2 text-xs font-black <?php echo e($activeStatus === $status ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($status); ?></a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php ($statusClass = match($order->order_status) {
                        'Completed' => 'bg-emerald-50 text-emerald-700',
                        'Cancelled' => 'bg-red-50 text-red-700',
                        'Shipped' => 'bg-violet-50 text-violet-700',
                        'Processing' => 'bg-blue-50 text-blue-700',
                        default => 'bg-amber-50 text-amber-700',
                    }); ?>
                    <div class="flex flex-col gap-4 border-b border-slate-100 p-5 last:border-b-0 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-receipt"></i></span>
                            <div class="min-w-0">
                                <a href="<?php echo e(route('account.order', $order)); ?>" class="font-black text-slate-950 hover:text-indigo-600">Order #<?php echo e($order->order_id); ?></a>
                                <p class="mt-1 text-xs text-slate-500"><?php echo e(optional($order->created_at)->format('d M Y, h:i A')); ?></p>
                                <p class="mt-2 text-sm text-slate-600"><?php echo e($order->payment_method ?: 'Payment method not specified'); ?> · <?php echo e($order->payment_status); ?></p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                            <span class="rounded-full px-3 py-1 text-xs font-black <?php echo e($statusClass); ?>"><?php echo e($order->order_status); ?></span>
                            <span class="font-black text-slate-950">৳ <?php echo e(number_format($order->total_amount, 2)); ?></span>
                            <a href="<?php echo e(route('account.order', $order)); ?>" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-black text-slate-700 hover:border-indigo-200 hover:text-indigo-700">View details</a>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="p-12 text-center">
                        <span class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-slate-100 text-slate-400"><i class="fa-solid fa-box-open text-2xl"></i></span>
                        <h2 class="mt-4 text-lg font-black">No matching orders</h2>
                        <p class="mt-1 text-sm text-slate-500">Try another status or start shopping.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->hasPages()): ?>
                <div class="mt-5"><?php echo e($orders->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/account/orders.blade.php ENDPATH**/ ?>