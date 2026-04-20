<?php

use App\Models\User;

test('admin dapat mengakses halaman pulse', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/pulse')
        ->assertSuccessful();
});

test('user biasa tidak dapat mengakses pulse', function () {
    $user = User::factory()->create(); // role default: User

    $this->actingAs($user)
        ->get('/pulse')
        ->assertForbidden();
});

test('instructor tidak dapat mengakses pulse', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get('/pulse')
        ->assertForbidden();
});

test('guest tidak dapat mengakses pulse', function () {
    // Pulse mengembalikan 403 untuk guest (tidak redirect ke /login)
    $this->get('/pulse')
        ->assertForbidden();
});
