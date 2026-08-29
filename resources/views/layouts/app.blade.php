<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? \App\Models\Setting::get('general.site_name', 'Optimum Biomedical') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @livewireStyles
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
    @include('partials.header')
    @include('partials.category-sidebar')

    <main class="w-full min-w-0 overflow-x-hidden pb-24 pt-20 sm:pb-0 lg:pt-28">
        @if(session('success'))
            <div class="mx-auto mt-3 max-w-7xl px-4">
                <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="mx-auto mt-3 max-w-7xl px-4">
                <div class="rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')
    @livewireScripts
    @stack('scripts')
</body>
</html>
