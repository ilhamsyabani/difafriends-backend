<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    /**
     * Heading harus cocok dengan key yang dibaca UsersImport (WithHeadingRow).
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'role',
            'phone',
            'city',
            'gender',
        ];
    }

    /**
     * Contoh baris agar admin paham format yang diharapkan.
     * Password TIDAK diisi di sini — dibuat otomatis & dikirim via email.
     *
     * @return array<int, array<int, string>>
     */
    public function array(): array
    {
        return [
            ['Budi', 'Santoso', 'budi.santoso@example.com', 'user', '081234567890', 'Bandung', 'male'],
            ['Siti', 'Aminah', 'siti.aminah@example.com', 'instructor', '081298765432', 'Surabaya', 'female'],
        ];
    }
}
