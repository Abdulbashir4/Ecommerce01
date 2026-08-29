<?php
    $hasChildren = $node->relationLoaded('childrenRecursive') ? $node->childrenRecursive->isNotEmpty() : $node->children()->exists();
    $hasBrands = $node->relationLoaded('brands') ? $node->brands->isNotEmpty() : $node->brands()->exists();
    $hasMenu = $hasChildren || $hasBrands;
?>

<li class="category-tree-item group relative">
    <div class="flex items-center justify-between rounded-xl px-3 py-2.5 transition hover:bg-cyan-50">
        <a href="<?php echo e(route('shop', ['subcategory_id' => $node->subcategory_id])); ?>" class="min-w-0 flex-1 truncate text-slate-700 hover:text-cyan-700"><?php echo e($node->subcategory_name); ?></a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMenu): ?>
            <button type="button" class="category-tree-toggle ml-2 grid h-7 w-7 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-cyan-100 hover:text-cyan-700 lg:hidden" aria-label="Open <?php echo e($node->subcategory_name); ?>">
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
            </button>
            <span class="category-tree-arrow hidden h-7 w-7 place-items-center rounded-lg text-slate-400 lg:grid"><i class="fa-solid fa-chevron-right text-[9px]"></i></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMenu): ?>
        <ul class="category-tree-children hidden list-none space-y-1 border-l border-cyan-100 bg-slate-50/70 py-1 pl-2 lg:static lg:mt-1 lg:ml-3 lg:w-auto lg:rounded-xl lg:border-l-2 lg:border-cyan-100 lg:bg-slate-50 lg:p-1.5 lg:shadow-none">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $node->childrenRecursive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php echo $__env->make('partials.category-sidebar-node', ['node' => $child, 'depth' => ($depth ?? 1) + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBrands): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $node->brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li>
                        <a href="<?php echo e(route('shop', ['brand_id' => $brand->brand_id])); ?>" class="block rounded-xl px-3 py-2.5 text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">
                            <i class="fa-solid fa-tag mr-2 text-[10px] text-cyan-500"></i><?php echo e($brand->brand_name); ?>

                        </a>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/partials/category-sidebar-node.blade.php ENDPATH**/ ?>