<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Customer account</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Welcome back, <?php echo e($user->name); ?> 👋</h1>
            <p class="mt-2 text-sm text-slate-500">Manage your orders, profile, addresses and saved products from one place.</p>
        </div>
        <a href="<?php echo e(route('account.orders')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-indigo-700">
            <i class="fa-solid fa-box-open"></i> View all orders
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
        <?php echo $__env->make('account.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="min-w-0 space-y-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->force_password_change): ?>
                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                        <div class="min-w-0">
                            <p class="font-black">Password change required</p>
                            <p class="mt-1 text-sm">For your account security, please update your password before continuing.</p>
                            <a href="<?php echo e(route('account.password.edit')); ?>" class="mt-3 inline-flex rounded-xl bg-amber-700 px-4 py-2 text-xs font-black text-white">Change password</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    ['total','Total Orders','fa-boxes-stacked','text-indigo-600','bg-indigo-50'],
                    ['pending','Pending','fa-clock','text-amber-600','bg-amber-50'],
                    ['processing','Processing','fa-gears','text-blue-600','bg-blue-50'],
                    ['shipped','Shipped','fa-truck-fast','text-violet-600','bg-violet-50'],
                    ['completed','Completed','fa-circle-check','text-emerald-600','bg-emerald-50'],
                    ['wishlist','Wishlist','fa-heart','text-rose-600','bg-rose-50'],
                    ['reviews','Reviews','fa-star','text-orange-600','bg-orange-50'],
                    ['addresses','Addresses','fa-location-dot','text-cyan-600','bg-cyan-50'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$key,$label,$icon,$color,$bg]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="grid h-10 w-10 place-items-center rounded-2xl <?php echo e($bg); ?> <?php echo e($color); ?>"><i class="fa-solid <?php echo e($icon); ?>"></i></span>
                            <span class="text-2xl font-black text-slate-950"><?php echo e($stats[$key]); ?></span>
                        </div>
                        <p class="mt-3 text-xs font-black uppercase tracking-wider text-slate-400"><?php echo e($label); ?></p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Recent Orders</h2>
                        <p class="mt-1 text-sm text-slate-500">Your latest purchases and their current status.</p>
                    </div>
                    <a href="<?php echo e(route('account.orders')); ?>" class="text-sm font-black text-indigo-600 hover:text-indigo-800">View all <i class="fa-solid fa-arrow-right ml-1"></i></a>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e(route('account.order', $order)); ?>" class="flex flex-col gap-3 border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-slate-100 text-slate-600"><i class="fa-solid fa-receipt"></i></span>
                            <div class="min-w-0">
                                <p class="truncate font-black text-slate-900">Order #<?php echo e($order->order_id); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e(optional($order->created_at)->format('d M Y, h:i A')); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <?php ($statusClass = match($order->order_status) {
                                'Completed' => 'bg-emerald-50 text-emerald-700',
                                'Cancelled' => 'bg-red-50 text-red-700',
                                'Shipped' => 'bg-violet-50 text-violet-700',
                                'Processing' => 'bg-blue-50 text-blue-700',
                                default => 'bg-amber-50 text-amber-700',
                            }); ?>
                            <span class="rounded-full px-3 py-1 text-xs font-black <?php echo e($statusClass); ?>"><?php echo e($order->order_status); ?></span>
                            <span class="font-black text-slate-950">৳ <?php echo e(number_format($order->total_amount, 2)); ?></span>
                        </div>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="p-10 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-box-open text-xl"></i></span>
                        <h3 class="mt-4 font-black text-slate-900">No orders yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Your completed purchases will appear here.</p>
                        <a href="<?php echo e(route('shop')); ?>" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white">Start shopping</a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/account/index.blade.php ENDPATH**/ ?>