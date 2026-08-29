<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo e($title ?? \App\Models\Setting::get('general.site_name', 'Optimum Biomedical')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

<style>
        html { scroll-behavior:smooth; }
        body { min-width:320px; }
        .responsive-scroll { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; overscroll-behavior-x:contain; }
        .responsive-scroll > * { min-width:max-content; }

        .category-sidebar { overscroll-behavior: contain; }
        .category-tree-item { position: relative; }
        @media (min-width: 1024px) {
            .category-tree-item:hover > .category-tree-children { display: block !important; }
            .category-tree-item:hover > div > .category-tree-arrow { color:#0891b2; background:#ecfeff; }
        }
        @media (max-width: 1023px) {
            .category-sidebar .category-tree-children { position:static !important; width:auto !important; margin-left:0 !important; box-shadow:none !important; border-radius:0 !important; border-top:1px solid #e2e8f0; border-right:0; border-bottom:0; border-left:1px solid #cffafe; }
        }
        @media (prefers-reduced-motion: reduce) { * { scroll-behavior:auto !important; transition-duration:0.01ms !important; animation-duration:0.01ms !important; } }
    </style>
</head>
<body class="overflow-x-hidden bg-white text-gray-900">
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.category-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="w-full min-w-0 overflow-x-hidden pb-24 pt-20 sm:pb-0 lg:pt-28">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mx-auto mt-3 max-w-7xl px-4">
                <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700"><?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="mx-auto mt-3 max-w-7xl px-4">
                <div class="rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><div><?php echo e($error); ?></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/layouts/app.blade.php ENDPATH**/ ?>