@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <a href="{{ route('admin.settings.services.index', ['type' => $type]) }}" class="text-sm font-bold text-slate-500 hover:text-indigo-600">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to services
        </a>
        <p class="mt-5 text-sm font-black uppercase tracking-widest text-indigo-600">{{ $service->exists ? 'Edit' : 'Create' }} Service</p>
        <h1 class="text-3xl font-black text-slate-900">{{ $service->exists ? $service->title : 'New Service' }}</h1>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ $service->exists ? route('admin.settings.services.update', $service) : route('admin.settings.services.store') }}" class="space-y-5">
        @csrf
        @if($service->exists) @method('PUT') @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 md:grid-cols-2">
                <label>
                    <span class="mb-2 block text-sm font-bold">Service type *</span>
                    <select name="type" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
                        @foreach(['hospital' => 'Hospital Bio Medical Service', 'other' => 'Other Service'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('type', $service->type) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="mb-2 block text-sm font-bold">Display order</span>
                    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3">
                </label>

                <label class="md:col-span-2">
                    <span class="mb-2 block text-sm font-bold">Title *</span>
                    <input name="title" required maxlength="180" value="{{ old('title', $service->title) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3">
                </label>

                <label class="md:col-span-2">
                    <span class="mb-2 block text-sm font-bold">Short description</span>
                    <textarea name="short_description" rows="3" maxlength="500" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('short_description', $service->short_description) }}</textarea>
                </label>

                <label class="md:col-span-2">
                    <span class="mb-2 block text-sm font-bold">Detailed description</span>
                    <textarea name="description" rows="7" maxlength="10000" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('description', $service->description) }}</textarea>
                </label>

                <label>
                    <span class="mb-2 block text-sm font-bold">Font Awesome icon</span>
                    <input name="icon" value="{{ old('icon', $service->icon) }}" placeholder="fa-hospital" class="w-full rounded-xl border border-slate-200 px-4 py-3">
                    <small class="mt-1 block text-xs text-slate-400">Example: fa-hospital, fa-screwdriver-wrench</small>
                </label>

                <label class="md:col-span-2">
                    <span class="mb-2 block text-sm font-bold">Features</span>
                    <textarea name="features" rows="6" placeholder="One feature per line" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('features', is_array($service->features) ? implode("\n", $service->features) : '') }}</textarea>
                </label>

                <label class="md:col-span-2">
                    <span class="mb-2 block text-sm font-bold">Service image</span>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-dashed border-indigo-300 bg-indigo-50/40 p-4">
                    @if($service->image)
                        <img src="{{ asset($service->image) }}" class="mt-4 h-36 w-full rounded-2xl object-cover sm:w-64" alt="Current image">
                    @endif
                </label>
            </div>

            <label class="mt-5 flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->exists ? $service->is_active : true)) class="h-5 w-5 rounded border-slate-300 text-indigo-600">
                <span class="text-sm font-bold">Publish this service</span>
            </label>
        </section>

        <div class="flex justify-end">
            <button class="rounded-xl bg-indigo-600 px-7 py-3 text-sm font-black text-white shadow-lg hover:bg-indigo-700">
                {{ $service->exists ? 'Save Changes' : 'Create Service' }}
            </button>
        </div>
    </form>
</div>
@endsection
