<?php

namespace App\Enums;

enum AttendanceFieldType: string
{
    case Text = 'text';
    case Phone = 'phone';
    case Email = 'email';
    case Number = 'number';
    case Textarea = 'textarea';
    case Select = 'select';
    case Signature = 'signature';

    public function label(): string
    {
        return match ($this) {
            AttendanceFieldType::Text => 'Teks',
            AttendanceFieldType::Phone => 'No. HP',
            AttendanceFieldType::Email => 'Email',
            AttendanceFieldType::Number => 'Angka',
            AttendanceFieldType::Textarea => 'Teks Panjang',
            AttendanceFieldType::Select => 'Pilihan',
            AttendanceFieldType::Signature => 'Tanda Tangan',
        };
    }

    /**
     * Laravel validation rule fragment for this field type.
     */
    public function validationRule(): string
    {
        return match ($this) {
            AttendanceFieldType::Email => 'email',
            AttendanceFieldType::Number => 'numeric',
            AttendanceFieldType::Phone => 'string|max:30',
            default => 'string',
        };
    }
}
