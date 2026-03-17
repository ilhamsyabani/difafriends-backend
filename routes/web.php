<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Inertia\Inertia;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });

// ── Dashboard default (user biasa) ─────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// ── Admin routes ───────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard');
        })->name('dashboard');
    });

// ── Instructor routes ──────────────────────────────────
Route::middleware(['auth', 'verified', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('instructor/Dashboard');
        })->name('dashboard');
    });

// ── Companion routes ───────────────────────────────────
Route::middleware(['auth', 'verified', 'role:companion'])
    ->prefix('companion')
    ->name('companion.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('companion/Dashboard');
        })->name('dashboard');
    });
require __DIR__ . '/settings.php';
