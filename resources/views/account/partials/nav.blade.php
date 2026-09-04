<aside class="lg:sticky lg:top-24 lg:self-start">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-5 text-white">
            <div class="flex items-center gap-3">
                @if(auth()->user()->profile_image)
                    <img src="{{ asset(auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="h-12 w-12 rounded-2xl object-cover ring-2 ring-white/20">
                @else
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/10 text-lg font-black">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                @endif
                <div class="min-w-0">
                    <p class="truncate font-black">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-300">{{ auth()->user()->phone }}</p>
                </div>
            </div>
        </div>
        <nav class="grid gap-1 p-3 text-sm font-bold">
            @php($nav = [
                ['account', 'fa-gauge-high', 'Dashboard', route('account')],
                ['account.orders', 'fa-box-open', 'My Orders', route('account.orders')],
                ['account.wishlist', 'fa-heart', 'Wishlist', route('account.wishlist')],
                ['account.reviews', 'fa-star', 'My Reviews', route('account.reviews')],
                ['account.addresses', 'fa-location-dot', 'Addresses', route('account.addresses')],
                ['account.profile.edit', 'fa-user', 'Profile', route('account.profile.edit')],
                ['account.password.edit', 'fa-lock', 'Password', route('account.password.edit')],
            ])
            @foreach($nav as [$routeName, $icon, $label, $url])
                <a href="{{ $url }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition {{ request()->routeIs($routeName) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid {{ $icon }} w-5 text-center"></i>{{ $label }}
                </a>
            @endforeach
            <a href="{{ route('shop') }}" class="mt-1 flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <i class="fa-solid fa-cart-shopping w-5 text-center"></i>Continue Shopping
            </a>
            <form method="POST" action="{{ url('/logout') }}" class="mt-1">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-red-600 transition hover:bg-red-50">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>Logout
                </button>
            </form>
        </nav>
    </div>
</aside>
