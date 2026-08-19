<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityRegistration>
 */
class ActivityRegistrationFactory extends Factory
{
    protected $model = ActivityRegistration::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activity_id' => Activity::factory(),
            'registered_at' => now(),
        ];
    }
}
