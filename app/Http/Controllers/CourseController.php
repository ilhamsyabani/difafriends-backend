<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::with(['category', 'instructor'])
            ->where('status', CourseStatus::Published);

        // Filter by kategori
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category)
                  ->orWhereHas('parent', function ($q2) use ($request) {
                      $q2->where('slug', $request->category);
                  });
            });
        }

        // Search by judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by harga
        if ($request->filled('price')) {
            match($request->price) {
                'free'    => $query->where('price', 0),
                'paid'    => $query->where('price', '>', 0),
                default   => null,
            };
        }

        // Sort
        match($request->get('sort', 'latest')) {
            'popular'    => $query->withCount('enrollments')->orderByDesc('enrollments_count'),
            'price_low'  => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };

        $courses = $query->paginate(12)->withQueryString();

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('courses/Index', [
            'courses'    => $courses,
            'categories' => $categories,
            'filters'    => $request->only(['search', 'category', 'price', 'sort']),
        ]);
    }

    public function show(Course $course): Response
    {
        // Pastikan hanya published yang bisa diakses publik
        abort_if($course->status !== CourseStatus::Published, 404);

        $course->load([
            'category',
            'instructor',
            'goals',
            'sections.lectures',
            'reviews' => fn($q) => $q->where('is_published', true)
                                     ->with('user')
                                     ->latest()
                                     ->limit(5),
        ]);

        return Inertia::render('courses/Show', [
            'course' => $course,
            'isEnrolled' => auth()->check()
                ? auth()->user()->isEnrolledIn($course)
                : false,
        ]);
    }
}
