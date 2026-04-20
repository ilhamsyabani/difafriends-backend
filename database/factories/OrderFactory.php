<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::factory()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
        ]);
        $price = fake()->randomElement([100000, 150000, 200000, 250000, 300000]);

        return [
            'user_id' => User::factory(),
            'orderable_type' => Course::class,
            'orderable_id' => $course->id,
            'item_name' => $course->title,
            'original_price' => $price,
            'discount_amount' => 0,
            'final_amount' => $price,
            'status' => OrderStatus::Paid,
            'invoice_number' => 'INV-'.strtoupper(fake()->unique()->bothify('####-????')),
            'expired_at' => now()->addDay(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => OrderStatus::Pending]);
    }

    public function paid(): static
    {
        return $this->state(['status' => OrderStatus::Paid]);
    }

    public function expired(): static
    {
        return $this->state([
            'status' => OrderStatus::Expired,
            'expired_at' => now()->subDay(),
        ]);
    }

    public function forBooking(): static
    {
        return $this->state(function () {
            $booking = Booking::factory()->create();

            return [
                'orderable_type' => Booking::class,
                'orderable_id' => $booking->id,
                'item_name' => 'Sesi Pendampingan',
            ];
        });
    }
}
