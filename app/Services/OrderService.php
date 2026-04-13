<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class OrderService
{
    public function createForCourse(User $user, Course $course): Order
    {
        // Cek sudah punya order pending atau sudah enroll
        $existingOrder = Order::where('user_id', $user->id)
            ->where('orderable_type', Course::class)
            ->where('orderable_id', $course->id)
            ->where('status', OrderStatus::Pending)
            ->first();

        if ($existingOrder) {
            return $existingOrder; // return yang sudah ada
        }

        $finalAmount = $course->discount_price ?? $course->price;

        $order = Order::create([
            'user_id' => $user->id,
            'orderable_type' => Course::class,
            'orderable_id' => $course->id,
            'item_name' => $course->title,
            'original_price' => $course->price,
            'discount_amount' => $course->price - $finalAmount,
            'final_amount' => $finalAmount,
            'status' => OrderStatus::Pending,
            'expired_at' => now()->addHour(),
            'invoice_number' => $this->generateInvoiceNumber(),
        ]);

        return $order;
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-'.date('Ymd').'-'.strtoupper(Str::random(6));
    }
}
