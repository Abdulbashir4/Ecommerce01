<aside class="lg:sticky lg:top-24 lg:self-start">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-5 text-white">
            <div class="flex items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->profile_image): ?>
                    <img src="<?php echo e(asset(auth()->user()->profile_image)); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="h-12 w-12 rounded-2xl object-cover ring-2 ring-white/20">
                <?php else: ?>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/10 text-lg font-black"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="min-w-0">
                    <p class="truncate font-black"><?php echo e(auth()->user()->name); ?></p>
                    <p class="truncate text-xs text-slate-300"><?php echo e(auth()->user()->phone); ?></p>
                </div>
            </div>
        </div>
        <nav class="grid gap-1 p-3 text-sm font-bold">
            <?php ($nav = [
                ['account', 'fa-gauge-high', 'Dashboard', route('account')],
                ['account.orders', 'fa-box-open', 'My Orders', route('account.orders')],
                ['account.wishlist', 'fa-heart', 'Wishlist', route('account.wishlist')],
                ['account.reviews', 'fa-star', 'My Reviews', route('account.reviews')],
                ['account.addresses', 'fa-location-dot', 'Addresses', route('account.addresses')],
                ['account.profile.edit', 'fa-user', 'Profile', route('account.profile.edit')],
                ['account.password.edit', 'fa-lock', 'Password', route('account.password.edit')],
            ]); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$routeName, $icon, $label, $url]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e($url); ?>" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition <?php echo e(request()->routeIs($routeName) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <i class="fa-solid <?php echo e($icon); ?> w-5 text-center"></i><?php echo e($label); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <a href="<?php echo e(route('shop')); ?>" class="mt-1 flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <i class="fa-solid fa-cart-shopping w-5 text-center"></i>Continue Shopping
            </a>
            <form method="POST" action="<?php echo e(url('/logout')); ?>" class="mt-1">
                <?php echo csrf_field(); ?>
                <button class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-red-600 transition hover:bg-red-50">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>Logout
                </button>
            </form>
        </nav>
    </div>
</aside>
<?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/account/partials/nav.blade.php ENDPATH**/ ?>