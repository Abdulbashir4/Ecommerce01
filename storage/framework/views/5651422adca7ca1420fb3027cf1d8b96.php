<?php
    $sidebarCategories = $categories ?? \App\Models\Category::with([
        'subcategories' => fn($q) => $q->whereNull('parent_subcategory_id')->with(['childrenRecursive', 'brands']),
    ])->orderBy('category_id')->get();
?>

<div id="categoryBackdrop" class="pointer-events-none fixed inset-0 z-[55] bg-slate-950/60 opacity-0 transition-opacity duration-300"></div>

<section id="mySection" class="category-sidebar fixed left-0 top-0 z-[60] h-screen w-[min(340px,92vw)] -translate-x-full overflow-y-auto border-r border-slate-200 bg-white shadow-2xl transition-transform duration-300 ease-out lg:w-[360px]">
    <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 px-4 py-4 text-white shadow-sm">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[.2em] text-cyan-100">Browse</p>
            <span class="text-sm font-black">SHOP BY CATEGORIES</span>
        </div>
        <button id="closeSidebar" type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-lg transition hover:bg-white/20" aria-label="Close categories">✕</button>
    </div>

    <ul class="category-tree m-0 list-none p-2 text-sm">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <li class="category-tree-item group relative border-b border-slate-100 last:border-0">
                <div class="flex items-center justify-between rounded-xl px-3 py-3 transition hover:bg-cyan-50">
                    <a href="<?php echo e(route('shop', ['category' => $category->category_id])); ?>" class="min-w-0 flex-1 truncate font-bold text-slate-800 hover:text-cyan-700"><?php echo e($category->category_name); ?></a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->subcategories->isNotEmpty()): ?>
                        <button type="button" class="category-tree-toggle ml-2 grid h-8 w-8 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-cyan-100 hover:text-cyan-700 lg:hidden" aria-label="Open <?php echo e($category->category_name); ?> subcategories">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                        <span class="category-tree-arrow hidden h-7 w-7 place-items-center rounded-lg text-slate-400 lg:grid"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->subcategories->isNotEmpty()): ?>
                    <ul class="category-tree-children hidden list-none space-y-1 border-l border-cyan-100 bg-slate-50/70 py-1 pl-2 lg:static lg:mt-1 lg:ml-3 lg:w-auto lg:rounded-xl lg:border-l-2 lg:border-cyan-100 lg:bg-slate-50 lg:p-1.5 lg:shadow-none">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $category->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php echo $__env->make('partials.category-sidebar-node', ['node' => $subcategory, 'depth' => 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </li>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </ul>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    const sidebar = document.getElementById('mySection');
    const backdrop = document.getElementById('categoryBackdrop');
    const toggleBtn = document.getElementById('toggleBtn');
    const mobileCategoryBtn = document.getElementById('mobileCategoryBtn');
    const closeBtn = document.getElementById('closeSidebar');
    if (!sidebar) return;

    const open = () => {
        sidebar.classList.remove('-translate-x-full');
        backdrop?.classList.remove('opacity-0','pointer-events-none');
        backdrop?.classList.add('opacity-100');
        document.body.classList.add('category-menu-open');
    };
    const close = () => {
        sidebar.classList.add('-translate-x-full');
        backdrop?.classList.add('opacity-0','pointer-events-none');
        backdrop?.classList.remove('opacity-100');
        document.body.classList.remove('category-menu-open');
    };

    toggleBtn?.addEventListener('click', e => { e.stopPropagation(); open(); });
    mobileCategoryBtn?.addEventListener('click', e => { e.stopPropagation(); open(); });
    closeBtn?.addEventListener('click', close);
    backdrop?.addEventListener('click', close);

    // Mobile: tap to expand each level. Desktop: CSS hover handles the same tree.
    sidebar.querySelectorAll('.category-tree-toggle').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            const li = btn.closest('li');
            const child = li?.querySelector(':scope > .category-tree-children');
            if (!child) return;
            child.classList.toggle('hidden');
            btn.querySelector('i')?.classList.toggle('rotate-90');
        });
    });

    sidebar.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
        if (window.innerWidth < 1024) close();
    }));

    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/partials/category-sidebar.blade.php ENDPATH**/ ?>