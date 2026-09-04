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
                ['User Management','Create, edit and delete admin/customer accounts.','fa-users','admin.users.index','users.view'],
                ['Roles & Permissions','Create roles and control permissions.','fa-user-shield','admin.roles.index','roles.view'],
                ['Audit Log','Review administrative security and change history.','fa-clock-rotate-left','admin.audit.index','audit.view'],
                ['Product Display','Choose which information appears on product cards.','fa-box-open','admin.settings.product-display','settings.product-display.view'],
                ['Layout Settings','Change grid/list layout, sidebar and product columns.','fa-table-cells-large','admin.settings.layout','settings.layout.view'],
                ['General Settings','Website identity, checkout, tax, shipping and maintenance.','fa-sliders','admin.settings.general','settings.general.view'],
                ['Gallery Management','Upload, edit, delete gallery images and change the public gallery style.','fa-images','admin.settings.gallery','gallery.view'],
                ['Service Management','Manage hospital and other services, including images and visibility.','fa-briefcase-medical','admin.settings.services.index','services.view'],
                ['Contact Messages','Read, update and delete messages submitted from Contact Us.','fa-envelope-open-text','admin.settings.contact-messages','contact-messages.view'],
                ['Company Profile','Edit company information, branding, contact details and homepage media.','fa-building','admin.company','company.view'],
            ];
        @endphp

        @foreach($cards as [$title,$desc,$icon,$route,$permission])
            @if(auth()->user()->hasPermission($permission))
            <a href="{{ route($route) }}" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white">
                    <i class="fa-solid {{ $icon }}"></i>
                </span>
                <h2 class="mt-5 text-lg font-black text-slate-900">{{ $title }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $desc }}</p>
                <span class="mt-5 inline-flex items-center text-sm font-black text-indigo-600">Open <i class="fa-solid fa-arrow-right ml-2"></i></span>
            </a>
            @endif
        @endforeach
    </div>
</div>
@endsection
