<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::query()
            ->with(['category', 'instructor'])
            ->withCount(['enrollments', 'lectures'])
            ->withAvg('reviews', 'rating');

        // 1. Fitur Pencarian (Search)
        $query->when($request->search, function ($q, $search) {
            $q->where('title', 'like', "%{$search}%");
        });

        // 2. Filter Kategori
        $query->when($request->category_id, function ($q, $categoryId) {
            $q->where('category_id', $categoryId);
        });

        // 3. Filter Status
        $query->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        // 4. Filter Rating
        $query->when($request->rating, function ($q, $rating) {
            $q->where(function ($subquery) {
                $subquery->selectRaw('AVG(rating)')
                    ->from('reviews')
                    ->whereColumn('reviewable_id', 'courses.id')
                    ->where('reviewable_type', Course::class)
                    ->whereNull('deleted_at');
            }, '>=', $rating);
        });

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['title', 'price', 'status'];

        if ($sort && in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        } else {
            $query->latest();
        }

        // 6. Pagination & Append Query String
        $courses = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/courses/Index', [
            'courses' => $courses,
            'categories' => $this->getCategories(),
            'filters' => $request->only(['search', 'category_id', 'status', 'rating', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/courses/Form', [
            'course' => null,
            'categories' => $this->getCategories(),
            'instructors' => $this->getInstructors(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'has_certificate' => 'boolean',
            'is_featured' => 'boolean',
            'prerequisites' => 'nullable|string',
            'status' => 'required|in:draft,review,published,archived',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('courses/thumbnails', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(5);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(Course $course): Response
    {
        return Inertia::render('admin/courses/Form', [
            'course' => $course,
            'categories' => $this->getCategories(),
            'instructors' => $this->getInstructors(),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'has_certificate' => 'boolean',
            'is_featured' => 'boolean',
            'prerequisites' => 'nullable|string',
            'status' => 'required|in:draft,review,published,archived',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(Course $course)
    {
        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'Kelas tidak bisa dihapus karena sudah ada yang mendaftar.');
        }

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    public function approve(Course $course)
    {
        $course->update(['status' => CourseStatus::Published->value]);

        return back()->with('success', "Kelas '{$course->title}' dipublikasikan.");
    }

    public function reject(Course $course)
    {
        $course->update(['status' => CourseStatus::Draft->value]);

        return back()->with('success', "Kelas '{$course->title}' dikembalikan ke draft.");
    }

    // ── Helpers ─────────────────────────────────────────
    private function getCategories()
    {
        return Category::with('parent')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
    }

    private function getInstructors()
    {
        return User::where('role', Roles::Instructor->value)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);
    }
}
