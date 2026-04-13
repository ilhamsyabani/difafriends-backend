<?php

use App\Enums\EnrollmentStatus;
use App\Enums\Roles;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => Roles::Admin->value]);
    $this->student = User::factory()->create(['role' => Roles::User->value]);
    $this->instructor = User::factory()->create(['role' => Roles::Instructor->value]);
    $this->category = Category::factory()->create();
    $this->course = Course::factory()->create([
        'instructor_id' => $this->instructor->id,
        'category_id' => $this->category->id,
    ]);
});

test('admin dapat melihat halaman peserta kelas', function () {
    $this->withoutVite();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.courses.enrollments.index', $this->course));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/courses/Enrollments')
            ->has('enrollments')
            ->has('course')
        );
});

test('admin dapat assign user ke kelas tanpa pembayaran', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.courses.enrollments.store', $this->course), [
            'user_id' => $this->student->id,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $this->student->id,
        'course_id' => $this->course->id,
        'order_id' => null,
        'status' => EnrollmentStatus::Active->value,
    ]);
});

test('tidak bisa assign user yang sudah terdaftar', function () {
    Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $this->course->id,
        'order_id' => null,
        'status' => EnrollmentStatus::Active->value,
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.courses.enrollments.store', $this->course), [
            'user_id' => $this->student->id,
        ]);

    $response->assertSessionHasErrors('user_id');
});

test('admin dapat menghapus enrollment dari kelas', function () {
    $enrollment = Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $this->course->id,
        'order_id' => null,
        'status' => EnrollmentStatus::Active->value,
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.courses.enrollments.destroy', [$this->course, $enrollment]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
});

test('non-admin tidak bisa mengakses halaman peserta', function () {
    $this->withoutVite();

    $this->actingAs($this->student)
        ->get(route('admin.courses.enrollments.index', $this->course))
        ->assertForbidden();
});
