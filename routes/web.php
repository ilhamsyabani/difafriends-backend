<?php

use App\Models\Category;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Companion\ScheduleController as CompanionScheduleController;
use App\Http\Controllers\Companion\BookingController as CompanionBookingController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'categories' => Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('sort_order')
            ->get(),
        'featuredCourses' => Course::with(['category', 'instructor'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->limit(6)
            ->get(),
    ]);
})->name('home');


// ── Dashboard default (user biasa) ─────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    //Order
    Route::post('/orders', [OrderController::class, 'store'])
         ->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])
         ->name('orders.index');

    //Lern
    Route::get('/learn/{course:slug}', [LearnController::class, 'show'])
              ->name('learn.show');
    Route::post('/learn/{course:slug}/progress', [LearnController::class, 'updateProgress'])
              ->name('learn.progress');
});

// ── Admin routes ───────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard');
        })->name('dashboard');
        Route::resource('users', AdminUserController::class);
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('courses', AdminCourseController::class);
        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');

        // Approve / reject course
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
        Route::patch('/courses/{course}/reject',  [AdminCourseController::class, 'reject'])->name('courses.reject');
    });

// ── Instructor routes ──────────────────────────────────
Route::middleware(['auth', 'verified', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('instructor/Dashboard');
        })->name('dashboard');
        Route::resource('courses', InstructorCourseController::class);
        Route::resource('courses.sections', InstructorCourseController::class);
        Route::resource('courses.sections.lectures', InstructorCourseController::class);

        Route::get('/courses/{course}/manage', function (App\Models\Course $course) {
            $course->load(['sections' => fn($q) => $q->orderBy('sort_order'), 'sections.lectures' => fn($q) => $q->orderBy('sort_order')]);
            return inertia('instructor/courses/Manage', ['course' => $course]);
        })->name('courses.manage');
    });

// ── Companion routes ───────────────────────────────────
Route::middleware(['auth', 'verified', 'role:companion'])
    ->prefix('companion')
    ->name('companion.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('companion/Dashboard');
        })->name('dashboard');
        Route::put('/schedules/{schedule}/toggle-status', [CompanionScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');
        Route::resource('schedules', CompanionScheduleController::class);
        Route::get('/bookings', [CompanionBookingController::class, 'index'])->name('bookings.index');
    });

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');


// Webhook Midtrans — tidak perlu auth (dari server Midtrans)
Route::post('/webhook/midtrans', [OrderController::class, 'webhook'])
     ->name('webhook.midtrans')
     ->withoutMiddleware(['web']);

require __DIR__ . '/settings.php';
