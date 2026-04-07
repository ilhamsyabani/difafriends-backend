<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('author')
            ->where('status', 'published') // WAJIB: Hanya ambil yang published
            ->latest();

        // Fitur pencarian opsional untuk pengunjung
        $query->when($request->search, function ($q, $search) {
            $q->where('title', 'like', "%{$search}%");
        });

        $articles = $query->paginate(9)->withQueryString();

        return Inertia::render('articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show($slug)
    {
        $article = Article::with('author')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $latestArticles = Article::where('status', 'published')
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('articles/Show', [
            'article' => $article,
        ]);
    }
}
