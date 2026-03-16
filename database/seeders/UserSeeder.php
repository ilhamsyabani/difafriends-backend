<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────
        User::create([
            'first_name' => 'Admin',
            'last_name'  => 'DifaFriends',
            'email'      => 'admin@difafriends.test',
            'password'   => Hash::make('password'),
            'phone'      => '081234567890',
            'city'       => 'Jakarta',
            'country'    => 'Indonesia',
            'role'       => Roles::Admin->value,
            'is_active'  => true,
        ]);

        // ── Instructor / Tentor ────────────────────────────
        User::create([
            'first_name' => 'Ahmad',
            'last_name'  => 'Fauzi',
            'email'      => 'instructor@difafriends.test',
            'password'   => Hash::make('password'),
            'phone'      => '081234567891',
            'city'       => 'Bandung',
            'country'    => 'Indonesia',
            'role'       => Roles::Instructor->value,
            'is_active'  => true,
        ]);

        // ── Guru Pendamping / Companion ────────────────────
        User::create([
            'first_name' => 'Siti',
            'last_name'  => 'Rahayu',
            'email'      => 'companion@difafriends.test',
            'password'   => Hash::make('password'),
            'phone'      => '081234567892',
            'city'       => 'Surabaya',
            'country'    => 'Indonesia',
            'role'       => Roles::Companion->value,
            'is_active'  => true,
        ]);

        // ── Regular User / Orang Tua ───────────────────────
        User::create([
            'first_name' => 'Rina',
            'last_name'  => 'Kusuma',
            'email'      => 'user@difafriends.test',
            'password'   => Hash::make('password'),
            'phone'      => '081234567893',
            'city'       => 'Yogyakarta',
            'country'    => 'Indonesia',
            'role'       => Roles::User->value,
            'is_active'  => true,
        ]);

        // ── Extra dummy users pakai Factory ───────────────
        // 5 instructor tambahan
        User::factory(5)->create([
            'role'      => Roles::Instructor->value,
            'is_active' => true,
        ]);

        // 5 companion tambahan
        User::factory(5)->create([
            'role'      => Roles::Companion->value,
            'is_active' => true,
        ]);

        // 20 regular user
        User::factory(20)->create([
            'role'      => Roles::User->value,
            'is_active' => true,
        ]);
    }
}
