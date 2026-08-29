@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div>
        <p class="text-sm font-black uppercase tracking-widest text-indigo-600">System</p>
        <h1 class="text-3xl font-black text-slate-900">Settings</h1>
        <p class="mt-1 text-sm text-slate-500">Manage users, products, website layout, services, company profile and contact messages.</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @php
            $cards = [
                ['User Management','Create, edit and delete admin/customer accounts.','fa-users','admin.users.index'],
                ['Product Display','Choose which information appears on product cards.','fa-box-open','admin.settings.product-display'],
                ['Layout Settings','Change grid/list layout and product columns.','fa-table-cells-large','admin.settings.layout'],
                ['General Settings','Website name, currency and maintenance mode.','fa-sliders','admin.settings.general'],
                ['Gallery Management','Upload, edit, delete gallery images and change the public gallery style.','fa-images','admin.settings.gallery'],
                ['Service Management','Manage Hospital Bio Medical and Other Services, including images and visibility.','fa-briefcase-medical','admin.settings.services.index'],
                ['Contact Messages','Read, update and delete messages submitted from Contact Us.','fa-envelope-open-text','admin.settings.contact-messages'],
                ['Company Profile','Edit company information, branding, contact details and homepage media.','fa-building','admin.company'],
            ];
        @endphp

        @foreach($cards as [$title,$desc,$icon,$route])
            <a href="{{ route($route) }}" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white">
                    <i class="fa-solid {{ $icon }}"></i>
                </span>
                <h2 class="mt-5 text-lg font-black text-slate-900">{{ $title }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $desc }}</p>
                <span class="mt-5 inline-flex items-center text-sm font-black text-indigo-600">Open <i class="fa-solid fa-arrow-right ml-2"></i></span>
            </a>
        @endforeach
    </div>
</div>
@endsection
