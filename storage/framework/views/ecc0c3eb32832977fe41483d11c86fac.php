<?php $__env->startSection('content'); ?>
<?php
    $toSideImageUrl = function ($path) {
        if (!$path) return null;
        $path = trim(str_replace('\\', '/', (string) $path));
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'uploads/')) return asset($path);
        if (str_starts_with($path, 'storage/')) return asset($path);
        return asset('uploads/side_image/' . basename($path));
    };
    $mainImages = array_values(array_filter(array_map($toSideImageUrl, is_array($company?->slider_image) ? $company->slider_image : [])));
    $rightImages = array_values(array_filter(array_map($toSideImageUrl, is_array($company?->right_slider) ? $company->right_slider : [])));
    $rightSplit = (int) ceil(count($rightImages) / 2);
    $promoOne = array_slice($rightImages, 0, $rightSplit);
    $promoTwo = array_slice($rightImages, $rightSplit);
?>
<section class="px-2 lg:px-8 mt-8">
    <div class="max-w-7xl mx-auto px-2 grid grid-cols-1 md:grid-cols-10 gap-2 lg:gap-6">
        <div class="md:col-span-7 relative overflow-hidden w-full lg:h-[390px] sm:h-[250px] h-[220px] rounded-2xl shadow-lg bg-black">
            <div id="sliderTrack" class="flex w-[200%] h-full transition-transform duration-700 ease-in-out">
                <img id="imgCurrent" class="w-1/2 h-full object-cover" src="" alt="">
                <img id="imgNext" class="w-1/2 h-full object-cover" src="" alt="">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-black/10 pointer-events-none"></div>
        </div>
        <div class="md:col-span-3 grid grid-cols-2 md:grid-cols-1 md:grid-rows-2 gap-2 lg:gap-4 lg:h-[390px] sm:h-[220px] h-[180px]">
            <img id="box1" class="w-full h-full object-cover rounded shadow" src="" alt="">
            <img id="box2" class="w-full h-full object-cover rounded shadow" src="" alt="">
        </div>
    </div>
</section>

<section id="category" class="py-12 px-6 lg:px-10 bg-gray-100">
    <h2 class="text-2xl font-bold text-center mb-6">Shop by Category</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e(route('shop', ['category' => $category->category_id])); ?>" class="block">
                <div class="group bg-white rounded shadow overflow-hidden">
                    <div class="h-28 bg-gray-100 overflow-hidden">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->category_image): ?>
                            <img src="<?php echo e(asset($category->category_image)); ?>" alt="<?php echo e($category->category_name); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-regular fa-image text-2xl"></i></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="p-3 text-center"><h3 class="text-sm font-semibold"><?php echo e($category->category_name); ?></h3></div>
                </div>
            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</section>

<section id="feature_products" class="mt-8 bg-gray-50 px-3 py-6 lg:px-8 lg:py-12">
    <?php
        $d = array_replace([
            'show_home_products' => true,
            'home_title' => 'Best Seller Products',
            'home_desktop_columns' => 4,
            'layout' => 'grid',
            'mobile_columns' => 1,
            'tablet_columns' => 2,
            'gap' => 5,
        ], is_array($productDisplay ?? null) ? $productDisplay : []);
        $homeDesktop = match((int) $d['home_desktop_columns']) { 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 5 => 'lg:grid-cols-5', 6 => 'lg:grid-cols-6', default => 'lg:grid-cols-4' };
        $homeTablet = match((int) $d['tablet_columns']) { 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4', default => 'sm:grid-cols-2' };
        $homeMobile = (int) $d['mobile_columns'] === 2 ? 'grid-cols-2' : 'grid-cols-1';
        $homeGap = match((int) $d['gap']) { 3 => 'gap-3', 4 => 'gap-4', 6 => 'gap-6', 8 => 'gap-8', default => 'gap-5' };
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d['show_home_products']): ?>
        <div class="mb-5 flex items-end justify-between gap-4">
            <h2 class="text-2xl font-bold"><?php echo e($d['home_title']); ?></h2>
            <a href="<?php echo e(route('shop')); ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View All <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>

        <div class="grid <?php echo e($homeMobile); ?> <?php echo e($homeTablet); ?> <?php echo e($homeDesktop); ?> <?php echo e($homeGap); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalecfc721726b8b5798826c96d529d8b59 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalecfc721726b8b5798826c96d529d8b59 = $attributes; } ?>
<?php $component = App\View\Components\ProductCard::resolve(['product' => $p] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\ProductCard::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalecfc721726b8b5798826c96d529d8b59)): ?>
<?php $attributes = $__attributesOriginalecfc721726b8b5798826c96d529d8b59; ?>
<?php unset($__attributesOriginalecfc721726b8b5798826c96d529d8b59); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalecfc721726b8b5798826c96d529d8b59)): ?>
<?php $component = $__componentOriginalecfc721726b8b5798826c96d529d8b59; ?>
<?php unset($__componentOriginalecfc721726b8b5798826c96d529d8b59); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="col-span-full text-gray-500">কোনো প্রডাক্ট পাওয়া যায়নি।</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const mainImages = <?php echo json_encode($mainImages, 15, 512) ?>;
const promoSets = {box1: <?php echo json_encode($promoOne, 15, 512) ?>, box2: <?php echo json_encode($promoTwo, 15, 512) ?>};
let index = 0;
const track = document.getElementById('sliderTrack');
const imgCurrent = document.getElementById('imgCurrent');
const imgNext = document.getElementById('imgNext');
if (mainImages.length > 0) {
    imgCurrent.src = mainImages[0];
    imgNext.src = mainImages[1 % mainImages.length];
}
setInterval(() => {
    if (mainImages.length < 2) return;
    track.style.transform = 'translateX(-50%)';
    setTimeout(() => {
        index = (index + 1) % mainImages.length;
        track.style.transition = 'none';
        track.style.transform = 'translateX(0)';
        imgCurrent.src = mainImages[index];
        imgNext.src = mainImages[(index + 1) % mainImages.length];
        track.offsetHeight;
        track.style.transition = 'transform 0.7s ease-in-out';
    }, 700);
}, 4000);
function startPromo(id, images) {
    if (!images.length) return;
    let i = 0; const el = document.getElementById(id); el.src = images[0];
    setInterval(() => { i = (i + 1) % images.length; el.src = images[i]; }, 4000);
}
startPromo('box1', promoSets.box1); startPromo('box2', promoSets.box2);


</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/home/index.blade.php ENDPATH**/ ?>