<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $year = (int) $request->get('year', now()->year);

        // Cache 1 jam per tahun — data historis tidak berubah sering
        $ttl = $year < now()->year ? 86400 : 3600;

        return Inertia::render('admin/reports/Index', [
            'year' => $year,
            'availableYears' => $this->availableYears(),
            'revenueChart' => Cache::remember("report:revenue:{$year}", $ttl, fn () => $this->revenueByMonth($year)),
            'enrollmentChart' => Cache::remember("report:enroll:{$year}", $ttl, fn () => $this->enrollmentsByMonth($year)),
            'userGrowth' => Cache::remember("report:users:{$year}", $ttl, fn () => $this->userGrowthByMonth($year)),
            'topCourses' => Cache::remember("report:top_courses:{$year}", $ttl, fn () => $this->topCoursesByRevenue()),
            'summary' => Cache::remember("report:summary:{$year}", $ttl, fn () => $this->summary($year)),
        ]);
    }

    /**
     * Export laporan revenue ke CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'between:2020,'.now()->year],
        ]);

        $year = (int) ($validated['year'] ?? now()->year);

        $orders = Order::with(['user', 'orderable'])
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->latest()
            ->get();

        $filename = "laporan-revenue-{$year}.csv";

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar baca UTF-8 dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Invoice', 'Nama User', 'Email', 'Item', 'Total (Rp)', 'Status', 'Tanggal']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $this->csvSafe($order->invoice_number),
                    $this->csvSafe($order->user?->first_name.' '.$order->user?->last_name),
                    $this->csvSafe($order->user?->email),
                    $this->csvSafe($order->item_name),
                    number_format((float) $order->final_amount, 0, ',', '.'),
                    $order->status->value ?? $order->status,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Cegah CSV Formula Injection (=, +, -, @, tab, carriage return
     * sebagai karakter pertama bisa dieksekusi Excel sebagai formula).
     */
    private function csvSafe(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        if (in_array($value[0] ?? '', ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }

    // ── Private helpers ────────────────────────────────────────

    private function revenueByMonth(int $year): array
    {
        // Tarik data mentah
        $orders = Order::select('created_at', 'final_amount')
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->get();

        // Kelompokkan dan jumlahkan via PHP Collection (Aman di semua Database)
        $grouped = $orders->groupBy(function($order) {
            return \Carbon\Carbon::parse($order->created_at)->format('n');
        })->map(function($group) {
            return $group->sum('final_amount');
        })->toArray();

        return $this->fillMonths($grouped);
    }

    private function enrollmentsByMonth(int $year): array
    {
        $enrollments = Enrollment::select('created_at')
            ->whereYear('created_at', $year)
            ->get();

        $grouped = $enrollments->groupBy(function($enrollment) {
            return \Carbon\Carbon::parse($enrollment->created_at)->format('n');
        })->map(function($group) {
            return $group->count();
        })->toArray();

        return $this->fillMonths($grouped, 0);
    }

    private function userGrowthByMonth(int $year): array
    {
        $users = User::select('created_at')
            ->whereYear('created_at', $year)
            ->get();

        $grouped = $users->groupBy(function($user) {
            return \Carbon\Carbon::parse($user->created_at)->format('n');
        })->map(function($group) {
            return $group->count();
        })->toArray();

        return $this->fillMonths($grouped, 0);
    }

    private function topCoursesByRevenue(): array
    {
        // Query ini aman di semua DB karena hanya menggunakan standar COUNT, SUM, GROUP BY, dan ORDER BY
        return Order::query()
            ->select(
                'item_name',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(final_amount) as total_revenue')
            )
            ->where('status', 'paid')
            ->where('orderable_type', Course::class)
            ->groupBy('item_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'name' => $r->item_name,
                'total_orders' => $r->total_orders,
                'total_revenue' => (float) $r->total_revenue,
            ])
            ->toArray();
    }

    private function summary(int $year): array
    {
        return [
            'total_revenue' => (float) Order::where('status', 'paid')->whereYear('created_at', $year)->sum('final_amount'),
            'total_orders' => Order::where('status', 'paid')->whereYear('created_at', $year)->count(),
            'total_enrollments' => Enrollment::whereYear('created_at', $year)->count(),
            'new_users' => User::whereYear('created_at', $year)->count(),
        ];
    }

    /** Isi bulan yang tidak ada data dengan nilai default. */
    private function fillMonths(array $data, mixed $default = 0.0): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $result = [];

        foreach (range(1, 12) as $m) {
            $result[] = [
                'month' => $months[$m - 1],
                'value' => $data[$m] ?? $default,
            ];
        }

        return $result;
    }

    private function availableYears(): array
    {
        $oldestOrderDate = Order::whereNull('deleted_at')->min('created_at');
        $earliest = $oldestOrderDate ? \Carbon\Carbon::parse($oldestOrderDate)->format('Y') : now()->year;

        return range((int) $earliest, now()->year);
    }
}
