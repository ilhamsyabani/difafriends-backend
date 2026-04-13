<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentStatus;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CourseEnrollmentController extends Controller
{
    public function index(Request $request, Course $course): Response
    {
        $enrollments = $course->enrollments()
            ->with('user:id,first_name,last_name,email,photo')
            ->latest('enrolled_at')
            ->paginate(15)
            ->withQueryString();

        $users = collect();
        if ($request->filled('search')) {
            $enrolledUserIds = $course->enrollments()->pluck('user_id');
            $users = User::where('role', Roles::User->value)
                ->where('is_active', true)
                ->where(function ($q) use ($request) {
                    $q->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                })
                ->whereNotIn('id', $enrolledUserIds)
                ->limit(10)
                ->get(['id', 'first_name', 'last_name', 'email']);
        }

        return Inertia::render('admin/courses/Enrollments', [
            'course' => $course->only(['id', 'title']),
            'enrollments' => $enrollments,
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('enrollments')->where('course_id', $course->id),
            ],
        ], [
            'user_id.unique' => 'User ini sudah terdaftar di kelas ini.',
        ]);

        Enrollment::create([
            'user_id' => $request->user_id,
            'course_id' => $course->id,
            'order_id' => null,
            'status' => EnrollmentStatus::Active->value,
            'enrolled_at' => now(),
        ]);

        return back()->with('success', 'User berhasil ditambahkan ke kelas.');
    }

    public function destroy(Course $course, Enrollment $enrollment): RedirectResponse
    {
        abort_if($enrollment->course_id !== $course->id, 404);

        $enrollment->delete();

        return back()->with('success', 'User berhasil dihapus dari kelas.');
    }
}
