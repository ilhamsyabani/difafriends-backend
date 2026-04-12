<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['payments' => fn ($q) => $q->latest()->limit(1)])
            ->latest()
            ->paginate(10);

        // Load orderable (Course atau Booking)
        $orders->each(function ($order) {
            $order->load('orderable');
        });

        return Inertia::render('user/Orders', [
            'orders' => $orders,
        ]);
    }

    public function enrollments(Request $request): Response
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->with([
                'course' => fn ($q) => $q->with(['instructor', 'category']),
                'course.certificates' => fn ($q) => $q->where('user_id', $request->user()->id),
            ])
            ->latest('enrolled_at')
            ->paginate(10);

        return Inertia::render('user/Enrollments', [
            'enrollments' => $enrollments,
        ]);
    }
}
