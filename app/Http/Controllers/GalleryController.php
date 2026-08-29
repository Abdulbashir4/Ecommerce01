<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\GallerySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private function storePublicGalleryImage($file): string
    {
        $dir = public_path('uploads/gallery');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = 'gallery_' . Str::lower(Str::random(24)) . '.' . $extension;
        $file->move($dir, $name);

        return 'uploads/gallery/' . $name;
    }

    private function deleteGalleryImage(?string $path): void
    {
        if (! $path) return;
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($path, 'uploads/')) {
            $file = public_path($path);
            if (is_file($file)) @unlink($file);
            return;
        }

        // Backward compatibility for older gallery/xxx.jpg records.
        Storage::disk('public')->delete($path);
        $legacy = public_path('uploads/gallery/' . basename($path));
        if (is_file($legacy)) @unlink($legacy);
    }

    private function defaultSettings(): GallerySetting
    {
        return new GallerySetting([
            'layout' => 'grid',
            'columns' => 3,
            'card_style' => 'rounded',
            'aspect_ratio' => '4/3',
            'show_title' => true,
            'show_description' => true,
            'show_overlay' => true,
            'autoplay' => false,
            'section_title' => 'Our Gallery',
            'section_subtitle' => 'Explore our products, services and business activities.',
        ]);
    }

    public function index()
    {
        $settings = GallerySetting::first() ?? $this->defaultSettings();

        $items = GalleryItem::where('status', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('gallery.index', compact('items', 'settings'));
    }

    public function adminIndex()
    {
        $items = GalleryItem::orderBy('sort_order')->latest()->paginate(24);
        $settings = GallerySetting::first() ?? $this->defaultSettings();

        return view('admin.settings.gallery', compact('items', 'settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $nextOrder = (int) GalleryItem::max('sort_order') + 1;

        foreach ($request->file('images', []) as $image) {
            $path = $this->storePublicGalleryImage($image);

            GalleryItem::create([
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'image_path' => $path,
                'sort_order' => $nextOrder++,
                'status' => true,
            ]);
        }

        return back()->with('success', 'Gallery image(s) added successfully.');
    }

    public function update(Request $request, GalleryItem $galleryItem)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $galleryItem->title = $data['title'] ?? null;
        $galleryItem->description = $data['description'] ?? null;
        $galleryItem->status = $request->boolean('status');

        if ($request->hasFile('image')) {
            $this->deleteGalleryImage($galleryItem->image_path);
            $galleryItem->image_path = $this->storePublicGalleryImage($request->file('image'));
        }

        $galleryItem->save();

        return back()->with('success', 'Gallery image updated successfully.');
    }

    public function destroy(GalleryItem $galleryItem)
    {
        $this->deleteGalleryImage($galleryItem->image_path);

        $galleryItem->delete();

        return back()->with('success', 'Gallery image deleted successfully.');
    }

    public function settingsUpdate(Request $request)
    {
        $data = $request->validate([
            'layout' => ['required', 'in:grid,masonry,slider'],
            'columns' => ['required', 'integer', 'in:2,3,4,5,6'],
            'card_style' => ['required', 'in:rounded,square,soft,shadow'],
            'aspect_ratio' => ['required', 'in:1/1,4/3,16/9,auto'],
            'section_title' => ['required', 'string', 'max:255'],
            'section_subtitle' => ['nullable', 'string', 'max:500'],
        ]);

        GallerySetting::updateOrCreate(
            ['id' => 1],
            [
                ...$data,
                'show_title' => $request->boolean('show_title'),
                'show_description' => $request->boolean('show_description'),
                'show_overlay' => $request->boolean('show_overlay'),
                'autoplay' => $request->boolean('autoplay'),
            ]
        );

        return back()->with('success', 'Gallery style settings saved successfully.');
    }
}
