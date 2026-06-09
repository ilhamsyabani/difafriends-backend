<?php

use App\Enums\ArticlesStatus;
use App\Enums\Roles;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CompanionController as AdminCompanionController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseEnrollmentController as AdminCourseEnrollmentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\QuizGradeController as AdminQuizGradeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
// ── Controllers ──────────────────────────────────────
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Companion\BookingController as CompanionBookingController;
use App\Http\Controllers\Companion\ScheduleController as CompanionScheduleController;
use App\Http\Controllers\CompanionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\LectureController as InstructorLectureController;
use App\Http\Controllers\Instructor\QuizGradeController;
use App\Http\Controllers\Instructor\QuizQuestionController;
use App\Http\Controllers\Instructor\SectionController as InstructorSectionController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ScalevWebhookController;
use App\Http\Controllers\UserOrderController;
use App\Models\Article;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Public Routes ─────────────────────────────────────
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
            ->map(fn ($c) => [
                'id' => $c->id,
                'first_name' => $c->first_name,
                'last_name' => $c->last_name,
                'photo' => $c->photo,
                'bio' => $c->bio ?? 'Berpengalaman mengajar anak difabel.',
                'city' => $c->city,
                'starting_price' => $c->schedules->min('price') ?? 50000,
            ]),
        'articles' => Article::with('author')
            ->where('status', ArticlesStatus::Published)
            ->latest()
            ->limit(3)
            ->get(),
    ]);
})->name('home');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/companions', [CompanionController::class, 'index'])->name('companions.index');
Route::get('/companions/{user}', [CompanionController::class, 'show'])->name('companions.show');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/verify/{number}', [CertificateController::class, 'verify'])->name('certificates.verify');

// ── Google OAuth (Socialite) ──────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

// Webhook Midtrans — no auth, no CSRF
Route::post('/webhook/midtrans', [OrderController::class, 'webhook'])
    ->name('webhook.midtrans')
    ->withoutMiddleware(['web']);

// Webhook Scalev — no auth, no CSRF
Route::post('/webhook/scalev', [ScalevWebhookController::class, 'handle'])
    ->name('webhook.scalev')
    ->withoutMiddleware(['web']);

// ── Authenticated Routes ──────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // Orders
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    // Learn
    Route::get('/learn/{course:slug}', [LearnController::class, 'show'])->name('learn.show');
    Route::post('/learn/{course:slug}/progress', [LearnController::class, 'updateProgress'])->name('learn.progress');

    // Reviews — auth + throttle 5 review per menit (cegah spam)
    Route::post('/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('reviews.store');

    // Quiz
    Route::get(
        '/learn/{course:slug}/quiz/{quiz}',
        [QuizController::class, 'show']
    )->name('quiz.show');
    Route::post(
        '/learn/{course:slug}/quiz/{quiz}/start',
        [QuizController::class, 'start']
    )->name('quiz.start');
    Route::post(
        '/learn/{course:slug}/quiz/{quiz}/submit',
        [QuizController::class, 'submit']
    )->name('quiz.submit');
    Route::get(
        '/learn/{course:slug}/quiz/{quiz}/result',
        [QuizController::class, 'result']
    )->name('quiz.result');

    // Bookings
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    // User area
    Route::get('/user/orders', [UserOrderController::class, 'index'])->name('user.orders');
    Route::get('/user/enrollments', [UserOrderController::class, 'enrollments'])->name('user.enrollments');

    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{number}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // Assessments
    Route::prefix('assessments')->middleware('auth')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('assessment.index');
        Route::get('/kesiapan', [AssessmentController::class, 'kesiapan'])->name('assessment.kesiapan');
        Route::get('/pemilihan', [AssessmentController::class, 'pemilihan'])->name('assessment.pemilihan');
        Route::post('/results', [AssessmentController::class, 'store'])->name('assessment.results');
        Route::delete('/results/{type}', [AssessmentController::class, 'destroy'])->name('assessment.destroy');
    });

    // Notifications
    // Endpoint ringan — hanya COUNT, dipakai polling 30 detik
    Route::get('/notifications/count', function (Request $request) {
        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    })->name('notifications.count');

    Route::get('/notifications', function (Request $request) {
        return response()->json([
            'notifications' => $request->user()->notifications()->limit(10)->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    // Whitelist field — jangan expose seluruh data object
                    'title' => $n->data['title'] ?? '',
                    'message' => $n->data['message'] ?? '',
                    'icon' => $n->data['icon'] ?? 'default',
                    'url' => $n->data['url'] ?? null,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at->diffForHumans(),
                ]),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    })->name('notifications.index');

    Route::post('/notifications/{id}/read', function (Request $request, string $id) {
        $request->user()->notifications()->findOrFail($id)->markAsRead();

        return response()->json(['success' => true]);
    })->name('notifications.read');

    Route::post('/notifications/read-all', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    })->name('notifications.read-all');
});

// ── Admin Routes ──────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            $dashboardStats = Cache::remember('admin_dashboard_stats', 300, function () {
                return [
                    'totalUsers' => User::count(),
                    'totalCourses' => Course::count(),
                    'totalOrders' => Order::count(),
                    'totalRevenue' => Order::where('status', 'paid')->sum('final_amount'),
                    'pendingCourses' => Course::where('status', 'review')->count(),
                ];
            });

            $dashboardStats['recentOrders'] = Order::with('user')
                ->latest()
                ->limit(10)
                ->get();

            return Inertia::render('admin/Dashboard', [
                'stats' => $dashboardStats,
            ]);
        })->name('dashboard');

        // Users — import/template harus didefinisikan sebelum resource
        // agar tidak tertangkap oleh route show (/users/{user}).
        Route::get('/users/template', [AdminUserController::class, 'template'])->name('users.template');
        Route::post('/users/import', [AdminUserController::class, 'import'])->name('users.import');
        Route::resource('users', AdminUserController::class);

        // Categories
        Route::resource('categories', AdminCategoryController::class);

        // Courses Admin
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::get(
            '/courses/{course}/manage',
            [AdminCourseController::class, 'manage']
        )->name('courses.manage');
        Route::patch(
            '/courses/{course}/approve',
            [AdminCourseController::class, 'approve']
        )->name('courses.approve');
        Route::patch(
            '/courses/{course}/reject',
            [AdminCourseController::class, 'reject']
        )->name('courses.reject');
        Route::get(
            '/courses/{course}/enrollments',
            [AdminCourseEnrollmentController::class, 'index']
        )->name('courses.enrollments.index');
        Route::post(
            '/courses/{course}/enrollments',
            [AdminCourseEnrollmentController::class, 'store']
        )->name('courses.enrollments.store');
        Route::delete(
            '/courses/{course}/enrollments/{enrollment}',
            [AdminCourseEnrollmentController::class, 'destroy']
        )->name('courses.enrollments.destroy');

        // Schedules Admin
        Route::resource('schedules', AdminScheduleController::class)->except(['show']);

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

        // Companions
        Route::get('/companions', [AdminCompanionController::class, 'index'])->name('companions.index');
        Route::get('/companions/{user}/schedules', [AdminCompanionController::class, 'schedules'])->name('companions.schedules');

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/meeting', [AdminBookingController::class, 'generateMeet'])->name('bookings.meeting');

        // Reports / Laporan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Activity Log
        Route::get('/activity-log', [AdminActivityLogController::class, 'index'])->name('activity-log.index');

        // Articles
        Route::resource('articles', App\Http\Controllers\Admin\ArticleController::class);

        // Quiz Grading
        Route::get(
            '/courses/{course}/quizzes/{quiz}/grade',
            [AdminQuizGradeController::class, 'index']
        )->name('courses.quiz.grade');

        Route::post(
            '/quiz-answers/{answer}/grade',
            [AdminQuizGradeController::class, 'grade']
        )->name('quiz.answer.grade');
    });

// ── Instructor Routes ─────────────────────────────────
Route::middleware(['auth', 'verified', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {

        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();
            $courses = Course::where('instructor_id', $user->id)
                ->withCount('enrollments')->latest()->get();

            return Inertia::render('instructor/Dashboard', [
                'stats' => [
                    'totalCourses' => $courses->count(),
                    'totalStudents' => $courses->sum('enrollments_count'),
                    'totalRevenue' => Order::where('status', 'paid')
                        ->whereIn('orderable_id', $courses->pluck('id'))
                        ->where('orderable_type', Course::class)
                        ->sum('final_amount'),
                    'courses' => $courses,
                ],
            ]);
        })->name('dashboard');

        Route::resource('courses', InstructorCourseController::class);
        Route::resource('courses.sections', InstructorSectionController::class)
            ->except(['index', 'show', 'create', 'edit']);
        Route::resource('courses.sections.lectures', InstructorLectureController::class)
            ->except(['index', 'show', 'create', 'edit']);

        Route::get('/courses/{course}/manage', function (Course $course) {
            $course->load([
                'sections' => fn ($q) => $q->orderBy('sort_order'),
                'sections.lectures' => fn ($q) => $q->orderBy('sort_order'),
                'sections.quiz',
            ]);

            return inertia('instructor/courses/Manage', ['course' => $course]);
        })->name('courses.manage');

        // Quiz
        Route::resource(
            'courses.sections.quiz',
            App\Http\Controllers\Instructor\QuizController::class
        )->except(['index', 'show']);

        Route::resource(
            'courses.sections.quiz.questions',
            QuizQuestionController::class
        )->except(['index', 'show']);

        Route::get(
            'courses/{course}/quizzes/{quiz}/grade',
            [QuizGradeController::class, 'index']
        )->name('quiz.grade');

        Route::post(
            'quiz-answers/{answer}/grade',
            [QuizGradeController::class, 'grade']
        )->name('quiz.answer.grade');
    });

// ── Companion Routes ──────────────────────────────────
Route::middleware(['auth', 'verified', 'role:companion'])
    ->prefix('companion')
    ->name('companion.')
    ->group(function () {

        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();

            return Inertia::render('companion/Dashboard', [
                'stats' => [
                    'totalBookings' => Booking::where('tutor_id', $user->id)->count(),
                    'upcomingBookings' => Booking::where('tutor_id', $user->id)
                        ->where('start_at', '>', now())
                        ->whereIn('status', ['confirmed', 'pending'])
                        ->count(),
                    'totalRevenue' => Order::where('status', 'paid')
                        ->whereHasMorph(
                            'orderable',
                            [Booking::class],
                            fn ($q) => $q->where('tutor_id', $user->id)
                        )
                        ->sum('final_amount'),
                    'recentBookings' => Booking::where('tutor_id', $user->id)
                        ->with(['student', 'schedule'])->latest()->limit(10)->get(),
                ],
            ]);
        })->name('dashboard');

        Route::resource('schedules', CompanionScheduleController::class);
        Route::put(
            '/schedules/{schedule}/toggle-status',
            [CompanionScheduleController::class, 'toggleStatus']
        )->name('schedules.toggle-status');

        Route::get('/bookings', [CompanionBookingController::class, 'index'])->name('bookings.index');
    });

require __DIR__.'/settings.php';

// ── Error page preview (dev only) ────────────────────────────────────────────
if (app()->environment('local')) {
    Route::get('/dev/errors/{status}', function (int $status) {
        return Inertia::render('ErrorPage', ['status' => $status]);
    })->where('status', '[0-9]+');
}
