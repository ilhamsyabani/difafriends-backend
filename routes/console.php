<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Laravel Pulse — simpan snapshot setiap menit
Schedule::command('pulse:snapshot')->everyMinute();

// Bersihkan data Pulse yang sudah tua (> 7 hari)
Schedule::command('pulse:clear --type=cache_interaction --older-than=7')->weekly();
