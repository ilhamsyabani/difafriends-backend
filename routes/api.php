<?php

use App\Http\Controllers\Api\LinkIdController;
use App\Http\Controllers\Api\LinkIdWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Link.id Payment ─────────────────────────────────────────────────────

    // Buat payment link (auth required)
    Route::post('/payments/linkid', [LinkIdController::class, 'createPaymentLink'])
        // ->middleware(['auth', 'verified'])
        ->name('api.payments.linkid.create');

    // Cek status payment link
    Route::get('/payments/linkid/{externalId}', [LinkIdController::class, 'getStatus'])
        // ->middleware(['auth', 'verified'])
        ->name('api.payments.linkid.status');

    // Webhook dari Link.id — no auth, no CSRF
    Route::post('/payments/linkid/webhook', [LinkIdWebhookController::class, 'handle'])
        ->name('api.payments.linkid.webhook');
        // ->withoutMiddleware(['web', 'auth']);
});
