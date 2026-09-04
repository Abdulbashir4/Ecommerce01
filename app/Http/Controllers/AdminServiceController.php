<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    private function validateData(Request $request, ?Service $service = null): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:hospital,other'],
            'title' => ['required', 'string', 'max:180'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'features' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $slugBase = Str::slug($data['title']) ?: 'service';
        $slug = $slugBase;
        $counter = 2;
        while (Service::query()
            ->where('slug', $slug)
            ->when($service, fn ($q) => $q->where($service->getKeyName(), '!=', $service->getKey()))
            ->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['features'] = collect(preg_split('/\r\n|\r|\n/', $data['features'] ?? ''))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        unset($data['image']);

        return $data;
    }

    public function index(Request $request)
    {
        $type = $request->query('type', 'hospital');
        if (!in_array($type, ['hospital', 'other'], true)) {
            $type = 'hospital';
        }

        $services = Service::query()
            ->where('type', $type)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin/settings/services/index', compact('services', 'type'));
    }

    public function create(Request $request)
    {
        $type = in_array($request->query('type'), ['hospital', 'other'], true)
            ? $request->query('type')
            : 'hospital';

        return view('admin/settings/services/form', [
            'service' => new Service(['type' => $type, 'is_active' => true]),
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $service = new Service($data);
        $this->handleImage($request, $service);
        $service->save();

        return redirect()
            ->route('admin.settings.services.index', ['type' => $service->type])
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('admin/settings/services/form', [
            'service' => $service,
            'type' => $service->type,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validateData($request, $service);
        $this->handleImage($request, $service);
        $service->fill($data)->save();

        return redirect()
            ->route('admin.settings.services.index', ['type' => $service->type])
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $path = $service->image;
        $service->delete();
        $this->deletePublicImage($path);

        return back()->with('success', 'Service deleted successfully.');
    }

    public function status(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return back()->with('success', 'Service visibility updated.');
    }

    private function handleImage(Request $request, Service $service): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $dir = public_path('uploads/services');
        File::ensureDirectoryExists($dir);

        if ($service->image) {
            $this->deletePublicImage($service->image);
        }

        $file = $request->file('image');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '-' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());

        $file->move($dir, $name);
        $service->image = 'uploads/services/' . $name;
    }

    private function deletePublicImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $normalized = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $full = public_path($normalized);
        $public = realpath(public_path());
        $target = realpath($full);

        if ($target && $public && str_starts_with($target, $public . DIRECTORY_SEPARATOR) && is_file($target)) {
            @unlink($target);
        }
    }
}
