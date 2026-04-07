<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Course;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderable'])->latest();

        // 1. Pencarian (berdasarkan Invoice atau Nama User)
        $query->when($request->search, function ($q, $search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q) use ($search) {
                  $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
              });
        });

        // 2. Filter Status (paid, pending, dll)
        $query->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        // 3. Filter Tipe (Course vs Booking)
        $query->when($request->type, function ($q, $type) {
            $modelClass = $type === 'course' ? Course::class : Booking::class;
            $q->where('orderable_type', $modelClass);
        });

        // Paginate 10 data per halaman dan bawa query string ke URL pagination
        $orders = $query->paginate(10)->withQueryString();

        return Inertia::render('admin/orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'type']),
        ]);
    }

    public function show(Order $order)
    {
        // Eager load relasi yang dibutuhkan
        $order->load([
            'user',
            'orderable',
            'payments',
        ]);

        return Inertia::render('admin/orders/Show', [
            'order' => $order,
        ]);
    }

    public function export(Request $request)
    {
        $fileName = 'Laporan_Transaksi_Difariends_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new OrdersExport($request->all()), $fileName);
    }
}
