@extends('layouts.app')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Gallery Image URL
    |--------------------------------------------------------------------------
    | Database-এ image_path যেভাবেই save করা থাকুক:
    | gallery/xxx.jpg
    | storage/gallery/xxx.jpg
    | /storage/gallery/xxx.jpg
    | uploads/gallery/xxx.jpg
    | /uploads/gallery/xxx.jpg
    | https://example.com/image.jpg
    |
    | সবগুলো handle করবে।
    */

    $imageUrl = function ($path) {
        if (!$path) {
            return asset('images/placeholder.jpg');
        }

        $path = trim($path);

        // Full URL হলে সরাসরি ব্যবহার
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Backslash থাকলে slash করে দেওয়া
        $path = str_replace('\\', '/', $path);

        // শুরুতে / থাকলে remove
        $path = ltrim($path, '/');

        /*
        |--------------------------------------------------------------------------
        | Already public path
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        /*
        |--------------------------------------------------------------------------
        | Laravel Storage path
        |--------------------------------------------------------------------------
        | যেমন:
        | gallery/image.jpg
        |
        | হবে:
        | /storage/gallery/image.jpg
        |--------------------------------------------------------------------------
        */

        // New gallery uploads are stored directly in public/uploads/gallery.
        if (str_starts_with($path, 'gallery/')) {
            $legacyName = basename($path);
            $publicFile = public_path('uploads/gallery/' . $legacyName);
            if (is_file($publicFile)) {
                return asset('uploads/gallery/' . $legacyName);
            }
        }

        return asset('storage/' . $path);
    };


    /*
    |--------------------------------------------------------------------------
    | Grid Columns
    |--------------------------------------------------------------------------
    */

    $cols = (int) ($settings->columns ?? 3);

    $gridCols = match($cols) {
        2 => 'sm:grid-cols-2',

        4 => 'sm:grid-cols-2 lg:grid-cols-4',

        5 => 'sm:grid-cols-2 lg:grid-cols-5',

        6 => 'sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6',

        default => 'sm:grid-cols-2 lg:grid-cols-3',
    };


    /*
    |--------------------------------------------------------------------------
    | Card Style
    |--------------------------------------------------------------------------
    */

    $cardClass = match($settings->card_style ?? 'default') {

        'square' => 'rounded-none',

        'soft' => 'rounded-xl',

        'shadow' => 'rounded-2xl shadow-xl',

        default => 'rounded-2xl',
    };


    /*
    |--------------------------------------------------------------------------
    | Image Aspect Ratio
    |--------------------------------------------------------------------------
    */

    $ratioClass = match($settings->aspect_ratio ?? '4/3') {

        '1/1' => 'aspect-square',

        '16/9' => 'aspect-video',

        'auto' => '',

        default => 'aspect-[4/3]',
    };

@endphp


<div class="min-h-screen bg-slate-50 py-10 sm:py-14">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


        {{-- ============================================================
             Gallery Header
        ============================================================= --}}

        <div class="mx-auto max-w-3xl text-center">

            <p class="text-sm font-black uppercase tracking-[0.25em] text-indigo-600">
                Optimum Biomedical
            </p>

            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                {{ $settings->section_title ?? 'Our Gallery' }}
            </h1>

            @if(!empty($settings->section_subtitle))

                <p class="mx-auto mt-4 max-w-2xl text-slate-500">
                    {{ $settings->section_subtitle }}
                </p>

            @endif

        </div>


        {{-- ============================================================
             Gallery Items
        ============================================================= --}}

        @if($items->count())


            {{-- ========================================================
                 SLIDER
            ========================================================= --}}

            @if(($settings->layout ?? 'grid') === 'slider')

                <div class="relative mt-10">

                    <div
                        id="gallerySlider"
                        class="flex snap-x gap-5 overflow-x-auto scroll-smooth pb-5 [scrollbar-width:thin]"
                    >

                        @foreach($items as $item)

                            @php
                                $src = $imageUrl($item->image_path);
                            @endphp

                            <article
                                class="gallery-slide group min-w-[85%] snap-center overflow-hidden
                                {{ $cardClass }}
                                border border-slate-200 bg-white shadow-sm
                                sm:min-w-[55%] lg:min-w-[35%]"
                            >

                                <div
                                    class="relative {{ $ratioClass }}
                                    overflow-hidden bg-slate-100"
                                >

                                    <img
                                        src="{{ $src }}"
                                        alt="{{ $item->title ?? 'Gallery image' }}"
                                        class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
                                    >


                                    {{-- Overlay --}}

                                    @if(
                                        ($settings->show_overlay ?? false)
                                        &&
                                        (
                                            ($settings->show_title ?? false)
                                            ||
                                            ($settings->show_description ?? false)
                                        )
                                    )

                                        <div
                                            class="absolute inset-x-0 bottom-0
                                            bg-gradient-to-t from-slate-950/90
                                            to-transparent
                                            p-5 pt-16"
                                        >

                                            @if(($settings->show_title ?? false) && $item->title)

                                                <h2 class="font-black text-white">
                                                    {{ $item->title }}
                                                </h2>

                                            @endif


                                            @if(($settings->show_description ?? false) && $item->description)

                                                <p class="mt-1 text-sm text-slate-200">
                                                    {{ $item->description }}
                                                </p>

                                            @endif

                                        </div>

                                    @endif

                                </div>


                                {{-- Normal Text --}}

                                @if(!($settings->show_overlay ?? false))

                                    @if(($settings->show_title ?? false) && $item->title)

                                        <h2 class="px-4 pt-4 font-black text-slate-900">
                                            {{ $item->title }}
                                        </h2>

                                    @endif


                                    @if(($settings->show_description ?? false) && $item->description)

                                        <p class="px-4 pb-4 pt-1 text-sm text-slate-500">
                                            {{ $item->description }}
                                        </p>

                                    @endif

                                @endif

                            </article>

                        @endforeach

                    </div>


                    {{-- Slider Controls --}}

                    <div class="mt-2 flex justify-center gap-2">

                        <button
                            type="button"
                            data-gallery-prev
                            class="grid h-10 w-10 place-items-center rounded-full
                            bg-white text-slate-700 shadow
                            ring-1 ring-slate-200 hover:bg-slate-100"
                            aria-label="Previous"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>


                        <button
                            type="button"
                            data-gallery-next
                            class="grid h-10 w-10 place-items-center rounded-full
                            bg-indigo-600 text-white shadow
                            hover:bg-indigo-700"
                            aria-label="Next"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                    </div>

                </div>


            {{-- ========================================================
                 MASONRY
            ========================================================= --}}

            @elseif(($settings->layout ?? 'grid') === 'masonry')

                <div
                    class="mt-10 columns-1 gap-5 sm:columns-2 lg:columns-{{ $cols }}"
                >

                    @foreach($items as $item)

                        @php
                            $src = $imageUrl($item->image_path);
                        @endphp

                        <article
                            class="mb-5 break-inside-avoid group overflow-hidden
                            {{ $cardClass }}
                            border border-slate-200 bg-white shadow-sm"
                        >

                            <div class="relative overflow-hidden bg-slate-100">

                                <img
                                    src="{{ $src }}"
                                    alt="{{ $item->title ?? 'Gallery image' }}"
                                    class="w-full object-cover transition duration-700 group-hover:scale-105"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
                                >


                                {{-- Overlay --}}

                                @if(
                                    ($settings->show_overlay ?? false)
                                    &&
                                    (
                                        ($settings->show_title ?? false)
                                        ||
                                        ($settings->show_description ?? false)
                                    )
                                )

                                    <div
                                        class="absolute inset-x-0 bottom-0
                                        bg-gradient-to-t from-slate-950/90
                                        to-transparent
                                        p-4 pt-12
                                        opacity-0 transition
                                        group-hover:opacity-100"
                                    >

                                        @if(($settings->show_title ?? false) && $item->title)

                                            <h2 class="font-black text-white">
                                                {{ $item->title }}
                                            </h2>

                                        @endif


                                        @if(($settings->show_description ?? false) && $item->description)

                                            <p class="mt-1 text-sm text-slate-200">
                                                {{ $item->description }}
                                            </p>

                                        @endif

                                    </div>

                                @endif

                            </div>


                            {{-- Text --}}

                            @if(!($settings->show_overlay ?? false))

                                @if(($settings->show_title ?? false) && $item->title)

                                    <h2 class="px-4 pt-4 font-black text-slate-900">
                                        {{ $item->title }}
                                    </h2>

                                @endif


                                @if(($settings->show_description ?? false) && $item->description)

                                    <p class="px-4 pb-4 pt-1 text-sm text-slate-500">
                                        {{ $item->description }}
                                    </p>

                                @endif

                            @endif

                        </article>

                    @endforeach

                </div>


            {{-- ========================================================
                 NORMAL GRID
            ========================================================= --}}

            @else

                <div class="mt-10 grid {{ $gridCols }} gap-5">

                    @foreach($items as $item)

                        @php
                            $src = $imageUrl($item->image_path);
                        @endphp


                        <article
                            class="group overflow-hidden
                            {{ $cardClass }}
                            border border-slate-200 bg-white shadow-sm"
                        >

                            <div
                                class="relative {{ $ratioClass }}
                                overflow-hidden bg-slate-100"
                            >

                                <img
                                    src="{{ $src }}"
                                    alt="{{ $item->title ?? 'Gallery image' }}"
                                    class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
                                >


                                {{-- Overlay --}}

                                @if(
                                    ($settings->show_overlay ?? false)
                                    &&
                                    (
                                        ($settings->show_title ?? false)
                                        ||
                                        ($settings->show_description ?? false)
                                    )
                                )

                                    <div
                                        class="absolute inset-x-0 bottom-0
                                        bg-gradient-to-t from-slate-950/90
                                        via-slate-950/30
                                        to-transparent
                                        p-5 pt-14
                                        opacity-0
                                        transition duration-300
                                        group-hover:opacity-100"
                                    >

                                        @if(($settings->show_title ?? false) && $item->title)

                                            <h2 class="font-black text-white">
                                                {{ $item->title }}
                                            </h2>

                                        @endif


                                        @if(($settings->show_description ?? false) && $item->description)

                                            <p class="mt-1 text-sm text-slate-200">
                                                {{ $item->description }}
                                            </p>

                                        @endif

                                    </div>

                                @endif

                            </div>


                            {{-- Text below image --}}

                            @if(!($settings->show_overlay ?? false))

                                @if(($settings->show_title ?? false) && $item->title)

                                    <h2 class="px-4 pt-4 font-black text-slate-900">
                                        {{ $item->title }}
                                    </h2>

                                @endif


                                @if(($settings->show_description ?? false) && $item->description)

                                    <p class="px-4 pb-4 pt-1 text-sm text-slate-500">
                                        {{ $item->description }}
                                    </p>

                                @endif

                            @endif

                        </article>

                    @endforeach

                </div>

            @endif


        {{-- ============================================================
             EMPTY GALLERY
        ============================================================= --}}

        @else

            <div
                class="mt-12 rounded-3xl border border-dashed
                border-slate-300 bg-white p-16 text-center"
            >

                <i class="fa-regular fa-images text-5xl text-slate-300"></i>

                <h2 class="mt-4 text-xl font-black text-slate-800">
                    Gallery is coming soon
                </h2>

                <p class="mt-2 text-slate-500">
                    Our products and business activities will appear here.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection



{{-- ================================================================
     SLIDER JAVASCRIPT
================================================================ --}}

@if(($settings->layout ?? 'grid') === 'slider')

    @push('scripts')

        <script>
            (() => {

                const slider = document.getElementById('gallerySlider');

                if (!slider) {
                    return;
                }


                const slides = slider.querySelectorAll('.gallery-slide');

                if (!slides.length) {
                    return;
                }


                let index = 0;


                const move = (direction) => {

                    index = (index + direction + slides.length) % slides.length;

                    slides[index].scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });

                };


                document
                    .querySelector('[data-gallery-prev]')
                    ?.addEventListener('click', () => move(-1));


                document
                    .querySelector('[data-gallery-next]')
                    ?.addEventListener('click', () => move(1));


                @if($settings->autoplay ?? false)

                    let timer = setInterval(() => move(1), 4000);


                    slider.addEventListener('mouseenter', () => {

                        clearInterval(timer);

                    });


                    slider.addEventListener('mouseleave', () => {

                        timer = setInterval(() => move(1), 4000);

                    });

                @endif

            })();
        </script>

    @endpush

@endif