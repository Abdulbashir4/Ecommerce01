@php
    $headerCompany = $company ?? \App\Models\CompanyInfo::query()->first();
    $headerImage = auth()->check() && auth()->user()->profile_image ? auth()->user()->profile_image : null;
    $cartCount = collect(session('cart', []))->sum('qty');
@endphp
<header class="fixed inset-x-0 top-0 z-50 w-full border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto flex min-h-16 w-full max-w-[1600px] items-center gap-2 px-3 py-2 sm:gap-3 sm:px-4 lg:px-8">
        <button id="mobileCategoryBtn" type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-yellow-400 hover:bg-yellow-50 lg:hidden" aria-label="Open categories">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="min-w-0 shrink-0">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                @if($headerCompany?->logo)
                    @php
                        $logoPath = ltrim(str_replace('\\', '/', (string) $headerCompany->logo), '/');
                        $logoUrl = filter_var($logoPath, FILTER_VALIDATE_URL) ? $logoPath : (str_starts_with($logoPath, 'uploads/') || str_starts_with($logoPath, 'storage/') ? asset($logoPath) : asset('uploads/side_image/' . basename($logoPath)));
                    @endphp
                    <img src="{{ $logoUrl }}" class="h-9 w-auto object-contain sm:h-11" alt="{{ $headerCompany->company_name }}" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden')">
                    <span class="hidden max-w-[130px] truncate text-sm font-black sm:text-lg">{{ $headerCompany->company_name }}</span>
                @else
                    <span class="max-w-[130px] truncate text-sm font-black sm:text-lg">{{ $headerCompany?->company_name ?? \App\Models\Setting::get('general.site_name', 'Optimum Biomedical') }}</span>
                @endif
            </a>
        </div>

        <div class="relative min-w-0 flex-1 lg:mx-auto lg:max-w-xl">
            <div class="flex w-full overflow-hidden rounded-full border border-yellow-400 bg-white shadow-sm focus-within:ring-4 focus-within:ring-yellow-100">
                <input type="text" id="searchInput" placeholder="Search products..." class="min-w-0 flex-1 bg-transparent px-3 py-2 text-sm outline-none sm:px-4" autocomplete="off">
                <button type="button" class="grid h-10 w-11 shrink-0 place-items-center bg-yellow-400 text-slate-900 transition hover:bg-yellow-300 sm:h-11 sm:w-12" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div id="searchResults" class="absolute left-0 top-full mt-2 max-h-80 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl hidden z-[60]"></div>
        </div>

        <div class="flex shrink-0 items-center gap-1 sm:gap-2 lg:gap-4">
            <a href="{{ route('cart') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-slate-700 transition hover:bg-slate-100" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping text-lg"></i>
                <span id="cartCount" class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $cartCount }}</span>
            </a>

            <div class="relative">
                <button id="profileMenuBtn" type="button" class="grid h-10 w-10 place-items-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50" aria-label="Profile menu" aria-expanded="false">
                    @if($headerImage)
                        <img src="{{ asset('uploads/'.$headerImage) }}" class="h-full w-full object-cover" alt="profile">
                    @else
                        <i class="fa-regular fa-user"></i>
                    @endif
                </button>
                <div id="profileMenu" class="absolute right-0 top-full mt-2 hidden w-48 overflow-hidden rounded-2xl border border-slate-200 bg-white p-1 shadow-2xl">
                    @auth
                        <a href="{{ url('/account') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Account</a>
                        <form method="post" action="{{ url('/logout') }}">@csrf<button class="block w-full rounded-xl px-4 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Logout</button></form>
                    @else
                        <a href="{{ route('login') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Login</a>
                        <a href="{{ url('/register') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <nav class="hidden items-center gap-6 border-t border-slate-100 px-4 py-3 lg:flex lg:px-8 xl:gap-10">
        <button id="toggleBtn" type="button" class="shrink-0 rounded-xl bg-yellow-400 px-4 py-2 text-sm font-bold transition hover:bg-yellow-300"><i class="fa-solid fa-bars mr-2"></i>Shop by Categories</button>
        <a href="{{ route('shop') }}" class="whitespace-nowrap text-sm font-medium hover:text-indigo-600">All Products</a>
        <a href="{{ route('hospital.services') }}" class="hidden whitespace-nowrap text-sm font-medium hover:text-indigo-600 xl:block">Hospital Bio Medical Service</a>
        <a href="{{ route('other.services') }}" class="hidden whitespace-nowrap text-sm font-medium hover:text-indigo-600 xl:block">Other Service</a>
        <a href="{{ route('profile') }}" class="whitespace-nowrap text-sm font-medium hover:text-indigo-600">Profile</a>
        <a href="{{ route('gallery') }}" class="hidden whitespace-nowrap text-sm font-medium xl:block">Gallery</a>
        @if($headerCompany?->facebook)<a href="{{ $headerCompany->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-square-facebook text-2xl"></i></a>@endif
        @if($headerCompany?->youtube)<a href="{{ $headerCompany->youtube }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube text-2xl"></i></a>@endif
        <a href="{{ route('contact') }}" class="hidden whitespace-nowrap text-sm font-medium hover:text-indigo-600 xl:block">Contact Us</a>
    </nav>
</header>

@push('scripts')
<script>
(() => {
    const input = document.getElementById('searchInput');
    const resultsBox = document.getElementById('searchResults');
    const profileBtn = document.getElementById('profileMenuBtn');
    const profileMenu = document.getElementById('profileMenu');
    const mobileCategoryBtn = document.getElementById('mobileCategoryBtn');

    if (input && resultsBox) {
        let controller;
        input.addEventListener('keyup', function (e) {
            const query = this.value.trim();
            if (e.key === 'Enter') {
                const firstLink = resultsBox.querySelector('a');
                if (firstLink) window.location.href = firstLink.href;
                return;
            }
            if (query.length < 2) { resultsBox.classList.add('hidden'); resultsBox.innerHTML = ''; return; }
            if (controller) controller.abort();
            controller = new AbortController();
            fetch('{{ url('/search') }}?q=' + encodeURIComponent(query), {signal: controller.signal, headers: {'Accept':'application/json'}})
                .then(res => res.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        resultsBox.innerHTML = '<div class="p-4 text-sm text-slate-500">No products found</div>';
                    } else {
                        resultsBox.innerHTML = data.map(item => `<a href="{{ url('/product') }}/${item.product_id}" class="block border-b border-slate-100 px-4 py-3 text-sm font-semibold hover:bg-slate-50 transition">${item.product_name}</a>`).join('');
                    }
                    resultsBox.classList.remove('hidden');
                })
                .catch(err => { if (err.name !== 'AbortError') console.error(err); });
        });
    }

    profileBtn?.addEventListener('click', e => { e.stopPropagation(); profileMenu?.classList.toggle('hidden'); profileBtn.setAttribute('aria-expanded', String(!profileMenu?.classList.contains('hidden'))); });
    document.addEventListener('click', e => {
        if (input && resultsBox && !input.contains(e.target) && !resultsBox.contains(e.target)) resultsBox.classList.add('hidden');
        if (profileMenu && profileBtn && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) profileMenu.classList.add('hidden');
    });
})();
</script>
@endpush
