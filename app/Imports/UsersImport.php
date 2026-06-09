<?php

namespace App\Imports;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    /**
     * User yang berhasil dibuat beserta password mentahnya (untuk kirim email).
     *
     * @var array<int, array{user: User, password: string}>
     */
    public array $created = [];

    /**
     * Pesan kesalahan per baris yang dilewati.
     *
     * @var array<int, string>
     */
    public array $errors = [];

    /**
     * Kolom yang valid untuk role pada file impor.
     *
     * @var array<int, string>
     */
    private array $validRoles = ['user', 'instructor', 'companion', 'admin'];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // +2: baris 1 adalah heading, index koleksi mulai dari 0.
            $line = $index + 2;

            $data = [
                'first_name' => trim((string) ($row['first_name'] ?? '')),
                'last_name' => trim((string) ($row['last_name'] ?? '')),
                'email' => strtolower(trim((string) ($row['email'] ?? ''))),
                'role' => strtolower(trim((string) ($row['role'] ?? 'user'))) ?: 'user',
                'phone' => trim((string) ($row['phone'] ?? '')) ?: null,
                'city' => trim((string) ($row['city'] ?? '')) ?: null,
                'gender' => strtolower(trim((string) ($row['gender'] ?? ''))) ?: null,
            ];

            // Lewati baris yang benar-benar kosong tanpa menghitungnya sebagai error.
            if ($data['first_name'] === '' && $data['email'] === '') {
                continue;
            }

            $validator = Validator::make($data, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', 'in:'.implode(',', $this->validRoles)],
                'phone' => ['nullable', 'string', 'max:20'],
                'city' => ['nullable', 'string', 'max:100'],
                'gender' => ['nullable', 'in:male,female,other'],
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Baris {$line}: ".implode(' ', $validator->errors()->all());

                continue;
            }

            $this->createUser($data);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createUser(array $data): void
    {
        $plainPassword = Str::password(10);

        $role = Roles::from($data['role']);
        unset($data['role']);

        $user = User::create([
            ...$data,
            'password' => Hash::make($plainPassword),
        ]);

        // role dan is_active sengaja tidak mass-assignable — set via forceFill.
        $user->forceFill(['role' => $role, 'is_active' => true])->save();

        $this->created[] = ['user' => $user, 'password' => $plainPassword];
    }
}
