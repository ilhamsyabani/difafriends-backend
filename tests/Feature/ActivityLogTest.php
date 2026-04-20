<?php

// Spatie Activity Log v5 menyimpan perubahan di `attribute_changes`, bukan `properties`
// - created: attribute_changes.attributes (nilai baru)
// - updated: attribute_changes.attributes (baru) + attribute_changes.old (lama)
// - deleted: attribute_changes.old (nilai lama)

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

// ── User Activity Log ──────────────────────────────────────────────────────

test('membuat user merekam activity log created', function () {
    $user = User::factory()->create();

    $log = Activity::where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('description', 'User created')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->attribute_changes->get('attributes'))->toHaveKey('email');
});

test('update field yang dilacak merekam activity log updated', function () {
    $user = User::factory()->create();
    $emailLama = $user->email;

    $user->update(['email' => 'baru@email.com']);

    $log = Activity::where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('description', 'User updated')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->attribute_changes->get('attributes')['email'])->toBe('baru@email.com')
        ->and($log->attribute_changes->get('old')['email'])->toBe($emailLama);
});

test('update user tanpa perubahan tidak merekam activity log', function () {
    $user = User::factory()->create();

    $countSebelum = Activity::where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->count();

    // Simpan dengan value yang sama — tidak ada perubahan dirty
    $user->email = $user->email;
    $user->save();

    expect(
        Activity::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->count()
    )->toBe($countSebelum);
});

test('hapus user merekam activity log deleted', function () {
    $user = User::factory()->create();

    $emailUser = $user->email;
    $user->delete();

    $log = Activity::where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('description', 'User deleted')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->attribute_changes->get('old')['email'])->toBe($emailUser);
});

// ── Course Activity Log ────────────────────────────────────────────────────

test('membuat course merekam activity log created', function () {
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);

    $log = Activity::where('subject_type', Course::class)
        ->where('subject_id', $course->id)
        ->where('description', 'Course created')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->attribute_changes->get('attributes'))->toHaveKey('title');
});

test('update judul course merekam activity log updated', function () {
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);

    $judulBaru = 'Judul Kelas Baru Yang Unik';
    $course->update(['title' => $judulBaru]);

    $log = Activity::where('subject_type', Course::class)
        ->where('subject_id', $course->id)
        ->where('description', 'Course updated')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->attribute_changes->get('attributes')['title'])->toBe($judulBaru);
});

test('hapus course merekam activity log deleted', function () {
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);

    $course->delete();

    $log = Activity::where('subject_type', Course::class)
        ->where('subject_id', $course->id)
        ->where('description', 'Course deleted')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->attribute_changes->get('old'))->toHaveKey('title');
});
