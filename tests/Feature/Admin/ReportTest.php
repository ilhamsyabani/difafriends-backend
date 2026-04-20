<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();

    // Bersihkan cache agar setiap test dapat data segar
    Cache::flush();
});

// ── Helper untuk buat order dengan field wajib ─────────────────────────────

function buatOrder(array $attrs = []): Order
{
    $user = $attrs['user_id'] ?? User::factory()->create()->id;
    $course = Course::factory()->create([
        'instructor_id' => User::factory()->instructor()->create()->id,
        'category_id' => Category::factory()->create()->id,
    ]);

    return Order::create(array_merge([
        'user_id' => $user,
        'orderable_type' => Course::class,
        'orderable_id' => $course->id,
        'item_name' => 'Kelas Test',
        'original_price' => 100000,
        'discount_amount' => 0,
        'final_amount' => 100000,
        'status' => OrderStatus::Paid,
        'invoice_number' => 'INV-'.uniqid(),
    ], $attrs));
}

// ── Akses halaman ──────────────────────────────────────────────────────────

test('admin dapat mengakses halaman laporan', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('admin/reports/Index')
            ->has('year')
            ->has('availableYears')
            ->has('revenueChart')
            ->has('enrollmentChart')
            ->has('userGrowth')
            ->has('topCourses')
            ->has('summary')
        );
});

test('non-admin tidak dapat mengakses halaman laporan', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

// ── Grafik bulanan — struktur 12 bulan ────────────────────────────────────

test('revenueChart selalu berisi 12 bulan dengan label yang benar', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertInertia(fn ($page) => $page
            ->has('revenueChart', 12)
            ->where('revenueChart.0.month', 'Jan')
            ->where('revenueChart.11.month', 'Des')
        );
});

test('enrollmentChart selalu berisi 12 bulan', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertInertia(fn ($page) => $page->has('enrollmentChart', 12));
});

test('userGrowth selalu berisi 12 bulan', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertInertia(fn ($page) => $page->has('userGrowth', 12));
});

// ── Data revenue bulanan akurat ────────────────────────────────────────────

test('revenueChart mencerminkan total order paid bulan ini', function () {
    $indexBulan = now()->month - 1;
    $tahunIni = now()->year;

    // 2 order paid bulan ini
    buatOrder(['user_id' => $this->admin->id, 'final_amount' => 100000, 'created_at' => now()->startOfMonth()]);
    buatOrder(['user_id' => $this->admin->id, 'final_amount' => 50000, 'created_at' => now()->startOfMonth()]);

    // 1 order pending — tidak dihitung dalam revenue
    buatOrder(['user_id' => $this->admin->id, 'status' => OrderStatus::Pending, 'final_amount' => 200000, 'created_at' => now()->startOfMonth()]);

    $this->actingAs($this->admin)
        ->get(route('admin.reports.index', ['year' => $tahunIni]))
        ->assertInertia(fn ($page) => $page
            ->where("revenueChart.{$indexBulan}.value", fn ($val) => $val == 150000)
        );
});

test('bulan tanpa order menampilkan revenue 0', function () {
    $tahunLalu = now()->year - 1;

    $this->actingAs($this->admin)
        ->get(route('admin.reports.index', ['year' => $tahunLalu]))
        ->assertInertia(fn ($page) => $page
            ->where('revenueChart.0.value', fn ($val) => $val == 0)
            ->where('revenueChart.5.value', fn ($val) => $val == 0)
            ->where('revenueChart.11.value', fn ($val) => $val == 0)
        );
});

// ── Data enrollment ────────────────────────────────────────────────────────

test('enrollmentChart mencerminkan jumlah enrollment bulan ini', function () {
    $indexBulan = now()->month - 1;
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);

    // Buat 3 enrollment bulan ini
    foreach (range(1, 3) as $_) {
        Enrollment::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now()->startOfMonth(),
            'created_at' => now()->startOfMonth(),
        ]);
    }

    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertInertia(fn ($page) => $page
            ->where("enrollmentChart.{$indexBulan}.value", 3)
        );
});

// ── User growth ────────────────────────────────────────────────────────────

test('userGrowth mencerminkan jumlah user baru bulan ini', function () {
    $indexBulan = now()->month - 1;

    User::factory()->count(2)->create(['created_at' => now()->startOfMonth()]);

    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertInertia(fn ($page) => $page
            ->where("userGrowth.{$indexBulan}.value", fn ($val) => $val >= 2)
        );
});

// ── Summary ────────────────────────────────────────────────────────────────

test('summary menghitung total revenue dan order tahun ini', function () {
    $tahunIni = now()->year;

    buatOrder(['user_id' => $this->admin->id, 'final_amount' => 75000, 'created_at' => now()]);
    buatOrder(['user_id' => $this->admin->id, 'final_amount' => 75000, 'created_at' => now()]);

    $this->actingAs($this->admin)
        ->get(route('admin.reports.index', ['year' => $tahunIni]))
        ->assertInertia(fn ($page) => $page
            ->where('summary.total_revenue', fn ($val) => $val >= 150000)
            ->where('summary.total_orders', fn ($val) => $val >= 2)
        );
});

// ── Export CSV ────────────────────────────────────────────────────────────

test('admin dapat export laporan CSV', function () {
    buatOrder([
        'user_id' => $this->admin->id,
        'final_amount' => 100000,
        'item_name' => 'Kelas Anak Berkebutuhan Khusus',
        'invoice_number' => 'INV-TEST-001',
        'created_at' => now(),
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.reports.export', ['year' => now()->year]))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('export CSV mengandung header kolom yang benar', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export', ['year' => now()->year]));

    $content = $response->streamedContent();

    expect($content)->toContain('Invoice')
        ->and($content)->toContain('Nama User')
        ->and($content)->toContain('Total (Rp)');
});
