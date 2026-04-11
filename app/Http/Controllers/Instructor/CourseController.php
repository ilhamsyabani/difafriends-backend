<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Enums\CourseStatus;
use Illuminate\Support\Str;


class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request): Response
     {
         $query = Course::query()
             ->select('id', 'title', 'slug', 'thumbnail', 'price', 'discount_price', 'status', 'is_featured', 'category_id', 'instructor_id')
             ->where('instructor_id', auth()->id())
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

         return Inertia::render('instructor/courses/Index', [
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
        return Inertia::render('instructor/courses/Form', [
            'course'     => null,
            'categories' => Category::with('parent')
                ->whereNotNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'description'      => 'required|string',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'has_certificate'  => 'boolean',
            'prerequisites'    => 'nullable|string',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Handle upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('courses/thumbnails', 'public');
        }

        $validated['instructor_id'] = $request->user()->id;
        $validated['status']        = CourseStatus::Draft->value;
        $validated['slug']          = Str::slug($validated['title']) . '-' . Str::random(5);

        Course::create($validated);

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Kelas berhasil dibuat. Tambahkan materi sekarang!');
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
    public function edit(Course $course)
    {
        abort_if($course->instructor_id !== auth()->user()->id, 403);

        return Inertia::render('instructor/courses/Form', [
            'course'     => $course,
            'categories' => Category::with('parent')
                ->whereNotNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'description'      => 'required|string',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'has_certificate'  => 'boolean',
            'prerequisites'    => 'nullable|string',
            'status'           => 'in:draft,review',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:2048', // ✅
        ]);

        // Handle upload thumbnail baru
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Kelas berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Course $course)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);
        abort_if($course->enrollments()->count() > 0, 422, 'Kelas tidak bisa dihapus karena sudah ada yang mendaftar.');

        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
