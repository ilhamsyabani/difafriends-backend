<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with('author')->latest();

        // 1. Filter Pencarian (Berdasarkan Judul atau Nama Penulis)
        $query->when($request->search, function ($q, $search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('author', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
        });

        // 2. Filter Status (Draft / Published)
        $query->when($request->status, function ($q, $status) {
            // Pastikan tidak memfilter jika statusnya "all" atau kosong
            if ($status !== 'all') {
                $q->where('status', $status);
            }
        });

        $articles = $query->paginate(5)->withQueryString();

        return Inertia::render('admin/articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/articles/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['title']).'-'.uniqid();
        $validated['author_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            // Mulai Proses Optimasi dengan Intervention
            $imageFile = $request->file('thumbnail');
            $filename = uniqid('article_').'.webp';
            $path = 'articles/'.$filename;

            // Inisiasi manager
            $manager = ImageManager::usingDriver(Driver::class);
            $image = $manager->decodePath($imageFile)->scaleDown(width: 1200);

            $encoded = $image->encode(new WebpEncoder(quality: 80));

            Storage::disk('public')->put($path, $encoded->toString());

            $validated['thumbnail'] = $path;
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): Response
    {
        return Inertia::render('admin/articles/Form', [
            'article' => $article,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus file lama
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            // Mulai Proses Optimasi dengan Intervention
            $imageFile = $request->file('thumbnail');
            $filename = uniqid('article_').'.webp'; // Paksa format jadi webp
            $path = 'articles/'.$filename;

            // Inisiasi manager
            $manager = ImageManager::usingDriver(Driver::class);
            $image = $manager->decodePath($imageFile)->scaleDown(width: 1200);

            $encoded = $image->encode(new WebpEncoder(quality: 80));

            Storage::disk('public')->put($path, $encoded->toString());

            $validated['thumbnail'] = $path;
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }
}
