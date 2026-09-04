<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index(string $type)
    {
        abort_unless(in_array($type, ['hospital', 'other'], true), 404);

        $services = Service::query()
            ->active()
            ->ofType($type)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $title = $type === 'hospital' ? 'Hospital Bio Medical Service' : 'Other Service';
        $subtitle = $type === 'hospital'
            ? 'Professional biomedical support for hospitals, clinics and healthcare facilities.'
            : 'Practical products, support and solutions for healthcare organizations.';

        return view('services.index', compact('services', 'title', 'subtitle', 'type'));
    }

    public function show(string $type, string $slug)
    {
        abort_unless(in_array($type, ['hospital', 'other'], true), 404);

        $service = Service::query()
            ->active()
            ->ofType($type)
            ->where('slug', $slug)
            ->firstOrFail();

        $title = $type === 'hospital' ? 'Hospital Bio Medical Service' : 'Other Service';

        return view('services.show', compact('service', 'title', 'type'));
    }
}
