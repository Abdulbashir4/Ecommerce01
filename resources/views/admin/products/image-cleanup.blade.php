@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-widest text-amber-600">Maintenance</p>
            <h1 class="text-3xl font-black text-slate-900">Unused Product Images</h1>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                These files exist in <code>public/uploads/products</code> but are not referenced by any product's thumbnail, featured image or gallery.
            </p>
        </div>
        <a href="{{ route('admin.products') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 hover:bg-slate-50">Back to Products</a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-black uppercase tracking-wider text-slate-500">Unused files</div>
            <div class="mt-2 text-3xl font-black text-amber-600">{{ number_format(count($files)) }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-black uppercase tracking-wider text-slate-500">Space to recover</div>
            <div class="mt-2 text-3xl font-black text-indigo-600">{{ number_format($totalSize / 1048576, 2) }} MB</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-black uppercase tracking-wider text-slate-500">Referenced image paths</div>
            <div class="mt-2 text-3xl font-black text-emerald-600">{{ number_format($referencedCount) }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-900">
        <strong>Safety:</strong> Only files that are not referenced by any product are listed. The cleanup does not touch database records or files outside <code>public/uploads/products</code>.
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center">
            <div>
                <h2 class="font-black text-slate-900">Files ready for cleanup</h2>
                <p class="text-xs text-slate-500">Review the list before permanently deleting the files.</p>
            </div>
            @if(count($files))
                <form method="POST" action="{{ route('admin.products.image-cleanup.run') }}" onsubmit="return confirm('Permanently delete all listed unused product images? This cannot be undone.');">
                    @csrf
                    <button class="rounded-xl bg-rose-600 px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-rose-700">
                        <i class="fa-solid fa-trash mr-2"></i> Delete All Unused Images
                    </button>
                </form>
            @endif
        </div>

        @if(count($files))
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="p-4">File</th>
                            <th class="p-4">Size</th>
                            <th class="p-4">Last modified</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($files as $file)
                            <tr class="hover:bg-slate-50">
                                <td class="p-4 font-semibold text-slate-800">{{ $file['name'] }}</td>
                                <td class="p-4 text-slate-600">{{ number_format($file['size'] / 1024, 1) }} KB</td>
                                <td class="p-4 text-slate-500">{{ $file['modified'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-10 text-center">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-check"></i></div>
                <h3 class="mt-4 font-black text-slate-900">No unused product images found</h3>
                <p class="mt-1 text-sm text-slate-500">Your product image directory is already clean.</p>
            </div>
        @endif
    </div>
</div>
@endsection
