<?php

use App\Models\Category;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\OrderController;
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

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });

// ── Dashboard default (user biasa) ─────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// ── Admin routes ───────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard');
        })->name('dashboard');
    });

// ── Instructor routes ──────────────────────────────────
Route::middleware(['auth', 'verified', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('instructor/Dashboard');
        })->name('dashboard');
    });

// ── Companion routes ───────────────────────────────────
Route::middleware(['auth', 'verified', 'role:companion'])
    ->prefix('companion')
    ->name('companion.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('companion/Dashboard');
        })->name('dashboard');
    });

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

// Order — perlu login
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])
         ->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])
         ->name('orders.index');
});

// Webhook Midtrans — tidak perlu auth (dari server Midtrans)
Route::post('/webhook/midtrans', [OrderController::class, 'webhook'])
     ->name('webhook.midtrans')
     ->withoutMiddleware(['web']); // skip CSRF

require __DIR__ . '/settings.php';
