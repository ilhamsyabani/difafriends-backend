<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    // /**
    //  * The current password being used by the factory.
    //  */
    // protected static ?string $password;

    // /**
    //  * Define the model's default state.
    //  *
    //  * @return array<string, mixed>
    //  */
    // public function definition(): array
    // {
    //     return [
    //         'name' => fake()->name(),
    //         'email' => fake()->unique()->safeEmail(),
    //         'email_verified_at' => now(),
    //         'password' => static::$password ??= Hash::make('password'),
    //         'remember_token' => Str::random(10),
    //         'two_factor_secret' => null,
    //         'two_factor_recovery_codes' => null,
    //         'two_factor_confirmed_at' => null,
    //     ];
    // }

    // /**
    //  * Indicate that the model's email address should be unverified.
    //  */
    // public function unverified(): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'email_verified_at' => null,
    //     ]);
    // }

    // /**
    //  * Indicate that the model has two-factor authentication configured.
    //  */
    // public function withTwoFactor(): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'two_factor_secret' => encrypt('secret'),
    //         'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
    //         'two_factor_confirmed_at' => now(),
    //     ]);
    // }
    public function definition(): array
    {
        $seed = fake()->numberBetween(1, 1000);

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => fake()->numerify('08##########'),
            'birth_date' => fake()->dateTimeBetween('-50 years', '-20 years'),
            'gender' => fake()->randomElement(['male', 'female']),
            'city' => fake()->randomElement([
                'Jakarta', 'Bandung', 'Surabaya',
                'Yogyakarta', 'Medan', 'Makassar',
                'Semarang', 'Palembang', 'Depok', 'Tangerang',
            ]),
            'country' => 'Indonesia',
            'role' => Roles::User->value,
            'is_active' => true,
            'photo' => "https://picsum.photos/seed/{$seed}/200/200",
            'remember_token' => Str::random(10),
        ];
    }

    // ── States — untuk generate role spesifik ─────────────

    public function admin(): static
    {
        return $this->state(['role' => Roles::Admin->value]);
    }

    public function instructor(): static
    {
        return $this->state(['role' => Roles::Instructor->value]);
    }

    public function companion(): static
    {
        return $this->state(['role' => Roles::Companion->value]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
