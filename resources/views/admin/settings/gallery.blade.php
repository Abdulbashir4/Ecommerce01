@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Settings / Gallery</p>
            <h1 class="text-3xl font-black text-slate-900">Gallery Management</h1>
            <p class="mt-1 text-sm text-slate-500">Add images repeatedly, edit/delete them, and control the public gallery style.</p>
        </div>
        <a href="{{ url('/gallery') }}" target="_blank"
           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-images mr-2"></i> View Gallery
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <div><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black">Add Gallery Images</h2>
            <p class="mb-4 text-sm text-slate-500">Select one or many images. Repeated uploads are appended to the gallery.</p>

            <form method="POST" action="{{ route('admin.settings.gallery.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="images[]" multiple accept="image/*"
                       class="block w-full rounded-xl border border-dashed border-indigo-300 bg-indigo-50/40 p-4 text-sm">
                <input name="title" placeholder="Title (optional)"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                <textarea name="description" rows="3" placeholder="Description (optional)"
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-5 py-3 text-sm font-black text-white shadow-lg">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Images
                </button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black">Gallery Style</h2>
            <p class="mb-4 text-sm text-slate-500">Changes affect the public gallery page.</p>

            <form method="POST" action="{{ route('admin.settings.gallery.settings') }}" class="space-y-4">
                @csrf

                <label class="block text-sm font-bold">Layout
                    <select name="layout" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                        @foreach(['grid'=>'Grid','masonry'=>'Masonry','slider'=>'Slider'] as $value => $label)
                            <option value="{{ $value }}" @selected($settings->layout === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-bold">Columns
                    <select name="columns" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                        @foreach([2,3,4,5,6] as $n)
                            <option value="{{ $n }}" @selected((int)$settings->columns === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-bold">Card Style
                    <select name="card_style" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                        @foreach(['rounded'=>'Rounded','square'=>'Square','soft'=>'Soft','shadow'=>'Strong Shadow'] as $value => $label)
                            <option value="{{ $value }}" @selected($settings->card_style === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-bold">Image Ratio
                    <select name="aspect_ratio" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                        @foreach(['1/1'=>'Square','4/3'=>'4:3','16/9'=>'16:9','auto'=>'Original'] as $value => $label)
                            <option value="{{ $value }}" @selected($settings->aspect_ratio === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <input name="section_title" value="{{ $settings->section_title }}" placeholder="Gallery title"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                <input name="section_subtitle" value="{{ $settings->section_subtitle }}" placeholder="Gallery subtitle"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">

                <div class="space-y-2 text-sm">
                    @foreach(['show_title'=>'Show title','show_description'=>'Show description','show_overlay'=>'Show hover overlay','autoplay'=>'Slider autoplay'] as $field => $label)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked($settings->$field)>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <button class="w-full rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white hover:bg-indigo-700">
                    <i class="fa-solid fa-paintbrush mr-2"></i> Save Gallery Style
                </button>
            </form>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h2 class="text-xl font-black">Gallery Images</h2>
        </div>

        <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($items as $item)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <img src="{{ str_starts_with(ltrim($item->image_path, '/'), 'uploads/') ? asset(ltrim($item->image_path, '/')) : (str_starts_with(ltrim($item->image_path, '/'), 'storage/') ? asset(ltrim($item->image_path, '/')) : (is_file(public_path('uploads/gallery/'.basename($item->image_path))) ? asset('uploads/gallery/'.basename($item->image_path)) : asset('storage/'.ltrim($item->image_path, '/')))) }}" class="aspect-[4/3] w-full object-cover" alt="{{ $item->title ?? 'Gallery image' }}">

                    <div class="space-y-3 p-4">
                        <form method="POST" action="{{ route('admin.settings.gallery.update', $item) }}" enctype="multipart/form-data" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input name="title" value="{{ $item->title }}" placeholder="Title"
                                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            <textarea name="description" rows="2" placeholder="Description"
                                      class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ $item->description }}</textarea>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs">
                            <label class="flex items-center gap-2 text-xs font-bold">
                                <input type="checkbox" name="status" value="1" @checked($item->status)>
                                Visible on public gallery
                            </label>
                            <button class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-xs font-black text-white hover:bg-indigo-700">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Save
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.settings.gallery.delete', $item) }}"
                              onsubmit="return confirm('Delete this gallery image?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-600 hover:bg-red-100">
                                <i class="fa-solid fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500">
                    <i class="fa-regular fa-images text-4xl"></i>
                    <p class="mt-3 font-bold">No gallery images yet.</p>
                </div>
            @endforelse
        </div>

        <div class="p-5">{{ $items->links() }}</div>
    </section>
</div>
@endsection
