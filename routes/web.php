<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\CompanionController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Companion\ScheduleController as CompanionScheduleController;
use App\Http\Controllers\Companion\BookingController as CompanionBookingController;
use Inertia\Inertia;
use Illuminate\Http\Request;

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
        'companions' => User::where('role', Roles::Companion->value)
                    ->where('is_active', true)
                    ->with('schedules')
                    ->inRandomOrder()
                    ->take(3)
                    ->get()
                    ->map(function ($companion) {
                        return [
                            'id' => $companion->id,
                            'first_name' => $companion->first_name,
                            'last_name' => $companion->last_name,
                            'photo' => $companion->photo ? $companion->photo : null,
                            'bio' => $companion->bio ?? 'Berpengalaman mengajar dan memberikan intervensi untuk anak difabel.',
                            'starting_price' => $companion->schedules->min('price') ?? 50000,
                        ];
                    }),
    ]);
})->name('home');


// ── Dashboard default (user biasa) ─────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    //Order
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    //Lern
    Route::get('/learn/{course:slug}', [LearnController::class, 'show'])->name('learn.show');
    Route::post('/learn/{course:slug}/progress', [LearnController::class, 'updateProgress'])->name('learn.progress');

    //Booking
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/user/orders', [UserOrderController::class, 'index'])->name('user.orders');
    Route::get('/user/enrollments', [UserOrderController::class, 'enrollments'])->name('user.enrollments');

    Route::get('/learn/{course:slug}/quiz/{quiz}',
        [\App\Http\Controllers\Student\QuizController::class, 'show']
    )->name('quiz.show');

    Route::post('/learn/{course:slug}/quiz/{quiz}/start',
        [\App\Http\Controllers\Student\QuizController::class, 'start']
    )->name('quiz.start');

    Route::post('/learn/{course:slug}/quiz/{quiz}/submit',
        [\App\Http\Controllers\Student\QuizController::class, 'submit']
    )->name('quiz.submit');

    Route::get('/learn/{course:slug}/quiz/{quiz}/result',
        [\App\Http\Controllers\Student\QuizController::class, 'result']
    )->name('quiz.result');

    //Notifications
    Route::get('/notifications', function (Request $request) {
        return response()->json([
            'notifications' => $request->user()
                ->notifications()
                ->limit(10)
                ->get()
                ->map(fn($n) => [
                    'id'        => $n->id,
                    'data'      => $n->data,
                    'read_at'   => $n->read_at,
                    'created_at'=> $n->created_at->diffForHumans(),
                ]),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    })->name('notifications.index');

    Route::post('/notifications/{id}/read', function (Request $request, string $id) {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read');

    Route::post('/notifications/read-all', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read-all');
});

// ── Admin routes ───────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard', [
                'stats' => [
                    'totalUsers'     => \App\Models\User::count(),
                    'totalCourses'   => \App\Models\Course::count(),
                    'totalOrders'    => \App\Models\Order::count(),
                    'totalRevenue'   => \App\Models\Order::where('status', 'paid')->sum('final_amount'),
                    'pendingCourses' => \App\Models\Course::where('status', 'review')->count(),
                    'recentOrders'   => \App\Models\Order::with('user')
                        ->latest()->limit(10)->get(),
                ],
            ]);
        })->name('dashboard');
        Route::resource('users', AdminUserController::class);
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('courses', AdminCourseController::class);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

        // Approve / reject course
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
        Route::patch('/courses/{course}/reject',  [AdminCourseController::class, 'reject'])->name('courses.reject');

        //ArticleController
        Route::resource('/articles', \App\Http\Controllers\Admin\ArticleController::class);
});

// ── Instructor routes ──────────────────────────────────

Route::middleware(['auth', 'verified', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {
        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();
            $courses = \App\Models\Course::where('instructor_id', $user->id)
                ->withCount('enrollments')
                ->latest()
                ->get();

            return Inertia::render('instructor/Dashboard', [
                'stats' => [
                    'totalCourses'  => $courses->count(),
                    'totalStudents' => $courses->sum('enrollments_count'),
                    'totalRevenue'  => \App\Models\Order::where('status', 'paid')
                        ->whereIn('orderable_id', $courses->pluck('id'))
                        ->where('orderable_type', \App\Models\Course::class)
                        ->sum('final_amount'),
                    'courses'       => $courses,
                ],
            ]);
        })->name('dashboard');
        Route::resource('courses', InstructorCourseController::class);
        Route::resource('courses.sections', InstructorCourseController::class);
        Route::resource('courses.sections.lectures', InstructorCourseController::class);

        Route::get('/courses/{course}/manage', function (App\Models\Course $course) {
            $course->load(['sections' => fn($q) => $q->orderBy('sort_order'), 'sections.lectures' => fn($q) => $q->orderBy('sort_order')]);
            return inertia('instructor/courses/Manage', ['course' => $course]);
        })->name('courses.manage');

        Route::resource('courses.sections.quiz',
            \App\Http\Controllers\Instructor\QuizController::class
        )->except(['index', 'show']);

        // Questions per quiz
        Route::resource('courses.sections.quiz.questions',
            \App\Http\Controllers\Instructor\QuizQuestionController::class
        )->except(['index', 'show']);

        // Grading esai
        Route::get('courses/{course}/quizzes/{quiz}/grade',
            [\App\Http\Controllers\Instructor\QuizGradeController::class, 'index']
        )->name('quiz.grade');

        Route::post('quiz-answers/{answer}/grade',
            [\App\Http\Controllers\Instructor\QuizGradeController::class, 'grade']
        )->name('quiz.answer.grade');

});

// ── Companion routes ───────────────────────────────────
Route::middleware(['auth', 'verified', 'role:companion'])->prefix('companion')
    ->name('companion.')
    ->group(function () {
        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();
            return Inertia::render('companion/Dashboard', [
                'stats' => [
                    'totalBookings'   => \App\Models\Booking::where('tutor_id', $user->id)->count(),
                    'upcomingBookings'=> \App\Models\Booking::where('tutor_id', $user->id)
                        ->where('start_at', '>', now())
                        ->whereIn('status', ['confirmed', 'pending'])
                        ->count(),
                    'totalRevenue'    => \App\Models\Order::where('status', 'paid')
                        ->whereHasMorph('orderable', [\App\Models\Booking::class], fn($q) =>
                            $q->where('tutor_id', $user->id)
                        )
                        ->sum('final_amount'),
                    'recentBookings'  => \App\Models\Booking::where('tutor_id', $user->id)
                        ->with(['student', 'schedule'])
                        ->latest()
                        ->limit(10)
                        ->get(),
                ],
            ]);
        })->name('dashboard');
        Route::put('/schedules/{schedule}/toggle-status', [CompanionScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');
        Route::resource('schedules', CompanionScheduleController::class);
        Route::get('/bookings', [CompanionBookingController::class, 'index'])->name('bookings.index');
});


Route::get('/articles', [\App\Http\Controllers\ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('articles.show');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/companions', [CompanionController::class, 'index'])->name('companions.index');
Route::get('/companions/{user}', [CompanionController::class, 'show'])->name('companions.show');


// Webhook Midtrans — tidak perlu auth (dari server Midtrans)
Route::post('/webhook/midtrans', [OrderController::class, 'webhook'])
     ->name('webhook.midtrans')
     ->withoutMiddleware(['web']);

require __DIR__ . '/settings.php';
