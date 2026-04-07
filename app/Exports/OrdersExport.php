<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Course;
use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    // Terima filter dari controller
    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // 1. Ambil Query (Otomatis di-chunk oleh Laravel Excel)
    public function query()
    {
        $query = Order::query()->with(['user', 'orderable'])->latest();

        $query->when($this->filters['search'] ?? null, function ($q, $search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q) use ($search) {
                  $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
              });
        });

        $query->when($this->filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        });

        $query->when($this->filters['type'] ?? null, function ($q, $type) {
            $modelClass = $type === 'course' ? Course::class : Booking::class;
            $q->where('orderable_type', $modelClass);
        });

        return $query;
    }

    // 2. Mapping Data (Baris per Baris dengan pengaman null)

    public function map($order): array
    {
        $orderType = $order->orderable_type ?? '';
        $tipe = str_contains($orderType, 'Course') ? 'Kelas' : 'Booking';

        // Ambil string dari Enum, gunakan null-safe jika status kosong
        $statusString = $order->status?->value ?? 'UNKNOWN';

        return [
            $order->invoice_number ?? '-',
            $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-',
            trim(($order->user?->first_name ?? '') . ' ' . ($order->user?->last_name ?? '')) ?: 'User Terhapus',
            $order->user?->email ?? '-',
            $order->item_name ?? '-',
            $tipe,
            $order->final_amount ?? 0,
            strtoupper($statusString),
        ];
    }

    // 3. Header Kolom di Excel
    public function headings(): array
    {
        return [
            'Invoice',
            'Tanggal',
            'Nama Pembeli',
            'Email',
            'Item Pembelian',
            'Tipe',
            'Total (IDR)',
            'Status',
        ];
    }
}
