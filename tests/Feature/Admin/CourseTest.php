<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Enums\Roles;
use App\Enums\CourseStatus;

// 1. ARRANGE GLOBAL: Fungsi ini berjalan SEBELUM setiap test dimulai
beforeEach(function () {
    // Kita buatkan user admin, instruktur, dan satu kategori
    $this->admin = User::factory()->create(['role' => Roles::Admin->value]);
    $this->instructor = User::factory()->create(['role' => Roles::Instructor->value]);
    $this->category = Category::factory()->create([
        'name' => 'Teknologi',
        'slug' => 'teknologi',
    ]);
});


// TEST 1: Memastikan halaman index bisa dibuka oleh admin
test('admin dapat melihat halaman daftar kelas', function () {
    // ARRANGE: Buat 5 data kelas dummy
    Course::factory(5)->create([
        'instructor_id' => $this->instructor->id,
        'category_id' => $this->category->id
    ]);

    // ACT: Simulasi login sebagai admin, lalu buka halaman index courses
    $response = $this->actingAs($this->admin)
        ->get(route('admin.courses.index'));

    // ASSERT: Pastikan halamannya merespon 200 OK
    $response->assertStatus(200);

    // ASSERT: Jika kamu pakai library Inertia Testing, bisa cek apakah datanya terkirim ke Vue
    $response->assertInertia(fn ($page) => $page
        ->component('admin/courses/Index')
        ->has('courses.data', 5) // Pastikan ada 5 data di array courses.data
    );
});


// TEST 2: Memastikan fitur tombol "Approve" berjalan
test('admin dapat melakukan approval pada kelas draft', function () {
    // ARRANGE: Buat 1 kursus dengan status masih 'draft'
    $course = Course::factory()->create([
        'instructor_id' => $this->instructor->id,
        'category_id' => $this->category->id,
        'status' => CourseStatus::Draft->value
    ]);

    // ACT: Simulasi login admin, dan lakukan POST ke route approve
    $response = $this->actingAs($this->admin)
            ->patch(route('admin.courses.approve', $course->id));

    // ASSERT: Pastikan tidak ada error dan kembali dengan redirect
    $response->assertRedirect();
    $response->assertSessionHas('success'); // Pastikan ada flash message success

    // ASSERT PALING PENTING: Cek langsung ke database apakah statusnya benar-benar berubah!
    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'status' => CourseStatus::Published->value,
    ]);
});

// TEST 3: Keamanan - Pastikan Instruktur tidak bisa approve kelasnya sendiri
test('instruktur tidak dapat melakukan approval kelas', function () {
    $course = Course::factory()->create([
        'instructor_id' => $this->instructor->id,
        'category_id' => $this->category->id,
        'status' => CourseStatus::Draft->value
    ]);

    // Lakukan aksi sebagai INSTRUKTUR, bukan admin
    $response = $this->actingAs($this->instructor)
        ->patch(route('admin.courses.approve', $course->id));

    // Pastikan ditolak (Forbidden 403 atau Redirect karena middleware role:admin)
    $response->assertStatus(403);
});
