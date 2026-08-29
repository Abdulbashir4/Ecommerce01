<?php
    $footerCompany = $company ?? \App\Models\CompanyInfo::query()->first();
    $footerCartCount = collect(session('cart', []))->sum('qty');
    $footerName = $footerCompany?->company_name ?: 'Optimum Biomedical';
    $footerPhone = $footerCompany?->phone;
    $footerEmail = $footerCompany?->email;
    $footerAddress = $footerCompany?->address;
    $footerLogo = $footerCompany?->logo;
    $footerAbout = $footerCompany?->about_us;
?>


<nav class="fixed inset-x-0 bottom-0 z-[100] border-t border-cyan-100 bg-white/95 shadow-[0_-8px_30px_rgba(15,23,42,.10)] backdrop-blur-md sm:hidden" aria-label="Mobile navigation">
    <ul class="mx-auto grid max-w-xl grid-cols-5">
        <li>
            <a href="<?php echo e(url('/')); ?>" class="flex flex-col items-center gap-1 px-1 py-2 text-slate-500 transition hover:text-cyan-600">
                <span class="footer-nav-icon flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-50"><i class="fa-solid fa-house text-sm"></i></span>
                <span class="text-[11px] font-semibold">Home</span>
            </a>
        </li>
        <li>
            <button type="button" onclick="document.getElementById('mySection')?.classList.remove('-translate-x-full')" class="flex w-full flex-col items-center gap-1 px-1 py-2 text-slate-500 transition hover:text-cyan-600">
                <span class="footer-nav-icon flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-50"><i class="fa-solid fa-layer-group text-sm"></i></span>
                <span class="text-[11px] font-semibold">Categories</span>
            </button>
        </li>
        <li>
            <a href="<?php echo e(route('shop')); ?>" class="flex flex-col items-center gap-1 px-1 py-2 text-slate-500 transition hover:text-cyan-600">
                <span class="footer-nav-icon flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-50"><i class="fa-solid fa-store text-sm"></i></span>
                <span class="text-[11px] font-semibold">Shop</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('cart')); ?>" class="relative flex flex-col items-center gap-1 px-1 py-2 text-slate-500 transition hover:text-cyan-600">
                <span class="footer-nav-icon relative flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-50">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span id="footerCartCount" class="footer-pulse absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm"><?php echo e($footerCartCount); ?></span>
                </span>
                <span class="text-[11px] font-semibold">Cart</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('profile')); ?>" class="flex flex-col items-center gap-1 px-1 py-2 text-slate-500 transition hover:text-cyan-600">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->check() && auth()->user()->profile_image): ?>
                    <img src="<?php echo e(asset('uploads/'.auth()->user()->profile_image)); ?>" class="h-8 w-8 rounded-xl border border-cyan-900/10 object-cover" alt="Profile">
                <?php else: ?>
                    <span class="footer-nav-icon flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-50"><i class="fa-regular fa-user text-sm"></i></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="text-[11px] font-semibold">Profile</span>
            </a>
        </li>
    </ul>
</nav>


<style>
    /* Premium footer motion */
    @keyframes footerFloat {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(0, -10px, 0); }
    }
    @keyframes footerGlow {
        0%, 100% { opacity: .45; transform: scale(1); }
        50% { opacity: .8; transform: scale(1.08); }
    }
    @keyframes footerShimmer {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(220%); }
    }
    @keyframes footerPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34,211,238,.18); }
        50% { box-shadow: 0 0 0 8px rgba(34,211,238,0); }
    }

    footer .footer-anim,
    footer a:not(.footer-social) {
        transition: transform .28s ease, color .28s ease, border-color .28s ease,
                    background-color .28s ease, box-shadow .28s ease;
    }

    footer .footer-social:hover {
        transform: translateY(-6px) rotate(-3deg) scale(1.08);
        box-shadow: 0 12px 28px rgba(0,0,0,.28);
    }

    footer .footer-social:active {
        transform: translateY(-1px) scale(.96);
    }

    footer .footer-nav-icon {
        transition: transform .25s ease, background-color .25s ease, color .25s ease,
                    box-shadow .25s ease;
    }

    footer a:hover .footer-nav-icon,
    footer button:hover .footer-nav-icon {
        transform: translateY(-3px) scale(1.08);
        box-shadow: 0 8px 18px rgba(8,145,178,.18);
    }

    footer .footer-float {
        animation: footerFloat 6s ease-in-out infinite;
    }

    footer .footer-glow {
        animation: footerGlow 5s ease-in-out infinite;
    }

    footer .footer-shimmer {
        position: relative;
        overflow: hidden;
    }

    footer .footer-shimmer::after {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 35%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
        transform: translateX(-120%);
        pointer-events: none;
    }

    footer .footer-shimmer:hover::after {
        animation: footerShimmer .9s ease;
    }

    footer .footer-pulse {
        animation: footerPulse 2.8s ease-in-out infinite;
    }

    @media (prefers-reduced-motion: reduce) {
        footer *, footer *::before, footer *::after {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<footer class="relative overflow-hidden border-t border-cyan-900/10 bg-gradient-to-br from-[#04131f] via-[#062235] to-[#10133a] pb-20 text-slate-200 sm:pb-0">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-500"></div>
    <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-cyan-500/10 blur-3xl footer-glow"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-violet-500/10 blur-3xl footer-glow"></div>
    
    <div class="mx-auto max-w-7xl px-5 py-12 sm:px-6 lg:px-8 lg:py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            
            <div class="lg:col-span-1">
                <a href="<?php echo e(url('/')); ?>" class="mb-5 inline-flex items-center gap-3">
                   <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerLogo): ?>
    <?php
        $footerLogoPath = ltrim(str_replace('\\', '/', (string) $footerLogo), '/');

        $footerLogoUrl = filter_var($footerLogoPath, FILTER_VALIDATE_URL)
            ? $footerLogoPath
            : (
                str_starts_with($footerLogoPath, 'uploads/')
                || str_starts_with($footerLogoPath, 'storage/')
                    ? asset($footerLogoPath)
                    : asset('uploads/side_image/' . basename($footerLogoPath))
            );
    ?>

    <img
        src="<?php echo e($footerLogoUrl); ?>"
        alt="<?php echo e($footerName); ?>"
        class="h-12 w-12 rounded-2xl bg-white object-contain p-1 shadow-sm"
        onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden')"
    >

    <span class="hidden h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-blue-600 to-indigo-600 text-xl font-black text-white shadow-lg shadow-cyan-900/30">
        OB
    </span>
<?php else: ?>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-blue-600 to-indigo-600 text-xl font-black text-white shadow-lg shadow-cyan-900/30">OB</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="max-w-[190px] text-lg font-extrabold leading-tight text-white"><?php echo e($footerName); ?></span>
                </a>
                <p class="max-w-sm text-sm leading-6 text-slate-400">
                    <?php echo e($footerAbout ? \Illuminate\Support\Str::limit(strip_tags($footerAbout), 190) : 'Professional biomedical equipment and healthcare solutions with dependable service and customer support.'); ?>

                </p>
                <div class="mt-6 flex items-center gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerCompany?->facebook): ?>
                        <a href="<?php echo e($footerCompany->facebook); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="footer-social footer-anim flex h-10 w-10 items-center justify-center rounded-xl border border-cyan-900/40 bg-[#0b2538] text-slate-200 transition hover:border-cyan-400 hover:bg-cyan-600 hover:text-white"><i class="fa-brands fa-facebook-f"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerCompany?->youtube): ?>
                        <a href="<?php echo e($footerCompany->youtube); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="footer-social footer-anim flex h-10 w-10 items-center justify-center rounded-xl border border-cyan-900/40 bg-[#0b2538] text-slate-200 transition hover:border-rose-400 hover:bg-rose-600 hover:text-white"><i class="fa-brands fa-youtube"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerPhone): ?>
                        <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $footerPhone)); ?>" aria-label="Call us" class="footer-social footer-anim flex h-10 w-10 items-center justify-center rounded-xl border border-cyan-900/40 bg-[#0b2538] text-slate-200 transition hover:border-emerald-400 hover:bg-emerald-600 hover:text-white"><i class="fa-solid fa-phone"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerEmail): ?>
                        <a href="mailto:<?php echo e($footerEmail); ?>" aria-label="Email us" class="footer-social footer-anim flex h-10 w-10 items-center justify-center rounded-xl border border-cyan-900/40 bg-[#0b2538] text-slate-200 transition hover:border-cyan-400 hover:bg-cyan-600 hover:text-white"><i class="fa-solid fa-envelope"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div>
                <h3 class="mb-5 text-sm font-bold uppercase tracking-[.16em] text-white">Quick Links</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Home</a></li>
                    <li><a href="<?php echo e(route('shop')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>All Products</a></li>
                    <li><a href="<?php echo e(route('cart')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Shopping Cart</a></li>
                    <li><a href="<?php echo e(route('hospital.services')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Hospital Bio Medical Service</a></li>
                    <li><a href="<?php echo e(route('other.services')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Other Service</a></li>
                    <li><a href="<?php echo e(route('profile')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Company Profile</a></li>
                    <li><a href="<?php echo e(route('contact')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Contact Us</a></li>
                    <li><a href="<?php echo e(auth()->check() ? url('/account') : route('login')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>My Account</a></li>
                    <li><a href="<?php echo e(route('login')); ?>" class="inline-flex items-center gap-2 transition hover:text-white"><i class="fa-solid fa-chevron-right text-[9px] text-cyan-400"></i>Login</a></li>
                </ul>
            </div>

            
            <div>
                <h3 class="mb-5 text-sm font-bold uppercase tracking-[.16em] text-white">Customer Care</h3>
                <ul class="space-y-4 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerPhone): ?>
                        <li class="flex items-start gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0b2538] text-cyan-300"><i class="fa-solid fa-phone"></i></span><div><p class="text-xs text-slate-500">Call us</p><a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $footerPhone)); ?>" class="font-semibold text-slate-200 hover:text-white"><?php echo e($footerPhone); ?></a></div></li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerEmail): ?>
                        <li class="flex items-start gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0b2538] text-cyan-400"><i class="fa-solid fa-envelope"></i></span><div><p class="text-xs text-slate-500">Email</p><a href="mailto:<?php echo e($footerEmail); ?>" class="break-all font-semibold text-slate-200 hover:text-white"><?php echo e($footerEmail); ?></a></div></li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerAddress): ?>
                        <li class="flex items-start gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0b2538] text-emerald-400"><i class="fa-solid fa-location-dot"></i></span><div><p class="text-xs text-slate-500">Visit us</p><p class="leading-5 text-slate-200"><?php echo e($footerAddress); ?></p></div></li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            
            <div>
                <h3 class="mb-5 text-sm font-bold uppercase tracking-[.16em] text-white">Why Choose Us</h3>
                <div class="space-y-3">
                    <div class="rounded-2xl border border-cyan-900/40 bg-[#0b2538]/70 p-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300"><i class="fa-solid fa-shield-halved"></i></span><div><p class="text-sm font-bold text-white">Quality Products</p><p class="mt-0.5 text-xs text-slate-500">Reliable biomedical solutions</p></div></div></div>
                    <div class="rounded-2xl border border-cyan-900/40 bg-[#0b2538]/70 p-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300"><i class="fa-solid fa-headset"></i></span><div><p class="text-sm font-bold text-white">Dedicated Support</p><p class="mt-0.5 text-xs text-slate-500">Friendly customer assistance</p></div></div></div>
                    <div class="rounded-2xl border border-cyan-900/40 bg-[#0b2538]/70 p-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-400/10 text-violet-300"><i class="fa-solid fa-truck-fast"></i></span><div><p class="text-sm font-bold text-white">Fast Service</p><p class="mt-0.5 text-xs text-slate-500">Smooth ordering experience</p></div></div></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="border-t border-cyan-900/40 bg-[#03111c]/60">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-5 text-xs text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <p>© <?php echo e(date('Y')); ?> <span class="font-semibold text-slate-200"><?php echo e($footerName); ?></span>. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-lock text-emerald-500"></i> Secure Shopping</span>
                <span class="hidden h-3 w-px bg-slate-700 sm:block"></span>
                <span>Developed by <span class="font-semibold text-slate-200">Abdul Basir</span></span>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/partials/footer.blade.php ENDPATH**/ ?>