<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class GalleryController extends Controller
{
    /**
     * Display a listing of the images and the upload form.
     */
    public function index()
    {
        $galleries = Gallery::orderBy('order')->get();

        return Inertia::render('admin/gallery/Index', [
            'galleries' => $galleries,
        ]);
    }

    /**
     * Store a newly uploaded image.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'max:5120'],
            'alt' => ['required', 'string', 'max:255'],
        ]);

        $file = $request->file('image');
        $filename = Str::uuid() . '.webp';
        $path = 'gallery/' . $filename;

        // Convert to WebP and resize
        $manager = ImageManager::usingDriver(Driver::class);
        $image = $manager->decodePath($file)->scaleDown(width: 1200);

        $encoded = $image->encode(new WebpEncoder(quality: 80));

        Storage::disk('public')->put($path, $encoded->toString());


        Gallery::create([
            'filename' => $file->getClientOriginalName(),
            'alt' => $validated['alt'],
            'path' => $path,
            'order' => Gallery::max('order') + 1,
        ]);

        return back()->with('success', 'Gambar berhasil diupload.');
    }

    /**
     * Remove the specified image.
     */
    public function destroy(Gallery $gallery)
    {
        Storage::disk('public')->delete($gallery->path);
        $gallery->delete();

        return back()->with('success', 'Gambar berhasil dihapus.');
    }
}
