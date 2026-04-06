<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\OrderPaidNotification;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use Inertia\Response;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request): Response
     {
         $query = Course::query()
             ->select('id', 'title', 'slug', 'thumbnail', 'price', 'discount_price', 'status', 'is_featured', 'category_id', 'instructor_id')
             ->with([
                 'category:id,name',
                 'instructor:id,first_name,last_name,photo'
             ])
             ->withCount(['enrollments', 'lectures'])
             ->withAvg('reviews', 'rating')
             ->when($request->category_id, function ($q, $categoryId) {
                 $q->where('category_id', $categoryId);
             })
             ->when($request->status, function ($q, $status) {
                 $q->where('status', $status);
             })
             ->when($request->rating, function ($q, $rating) {
                 $q->where(function ($subquery) {
                     $subquery->selectRaw('avg(rating)')
                              ->from('reviews')
                              ->whereColumn('reviewable_id', 'courses.id')
                              ->where('reviewable_type', Course::class)
                              // Tambahkan baris di bawah INI JIKA model Review memakai SoftDeletes
                              ->whereNull('deleted_at');
                 }, '>=', $rating);
             });

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
             'popular'    => $query->orderByDesc('enrollments_count'),
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

         return Inertia::render('admin/courses/Index', [
             'courses'    => $courses,
             'categories' => $categories,
             'filters'    => $request->only(['search', 'category_id', 'status', 'rating', 'price', 'sort']),
         ]);
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approve(Course $course)
    {
        $course->update(['status' => CourseStatus::Published->value]);
        $course->instructor->notify(new CourseApprovedNotification($course));

        return back()->with('success', "Kelas '{$course->title}' berhasil dipublikasikan.");
    }

    public function reject(Request $request, Course $course)
    {
        $request->validate(['reason' => 'nullable|string']);
        $course->update(['status' => CourseStatus::Draft->value]);

        return back()->with('success', "Kelas '{$course->title}' dikembalikan ke draft.");
    }
}
