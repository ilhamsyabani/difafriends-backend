<?php

namespace App\Enums;

enum Roles : string
{
    case User       = 'user';
    case Instructor = 'instructor';
    case Companion  = 'companion';
    case Admin      = 'admin';

    // ✅ Label untuk ditampilkan di UI
    public function label(): string
    {
        return match($this) {
            Roles::User       => 'Orang Tua / User',
            Roles::Instructor => 'Tentor',
            Roles::Companion  => 'Guru Pendamping',
            Roles::Admin      => 'Administrator',
        };
    }

    // ✅ Deskripsi role
    public function description(): string
    {
        return match($this) {
            Roles::User       => 'Dapat membeli kelas dan booking guru pendamping',
            Roles::Instructor => 'Dapat membuat dan mengelola kelas online',
            Roles::Companion  => 'Dapat menerima booking sesi pendampingan',
            Roles::Admin      => 'Akses penuh ke semua fitur platform',
        };
    }

    // ✅ Cek apakah role ini adalah provider (tentor atau guru)
    public function isProvider(): bool
    {
        return in_array($this, [Roles::Instructor, Roles::Companion]);
    }
}
