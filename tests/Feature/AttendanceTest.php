<?php

use App\Enums\Roles;
use App\Models\Activity;
use App\Models\AttendanceForm;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => Roles::Admin->value]);
});

test('admin dapat membuat kegiatan', function () {
    actingAs($this->admin)
        ->post(route('admin.activities.store'), [
            'name' => 'Webinar Nasional ABK',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-03',
            'location' => 'Zoom',
            'description' => null,
        ])
        ->assertRedirect(route('admin.activities.index'));

    expect(Activity::where('name', 'Webinar Nasional ABK')->exists())->toBeTrue();
});

test('membuat absensi untuk kegiatan 3 hari menghasilkan 3 sesi dengan token', function () {
    $activity = Activity::factory()->create([
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-03',
    ]);

    actingAs($this->admin)
        ->post(route('admin.activities.attendance-forms.store', $activity), [
            'title' => 'Absensi Peserta',
            'fields' => [
                ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
                ['key' => 'ttd', 'label' => 'Tanda Tangan', 'type' => 'signature', 'required' => true],
            ],
        ])
        ->assertRedirect();

    $form = AttendanceForm::first();

    expect($form->sessions)->toHaveCount(3);
    expect($form->sessions->pluck('token')->unique())->toHaveCount(3);
});

test('guest dapat melihat halaman absensi publik', function () {
    $session = AttendanceSession::factory()->create();

    get(route('attendance.public', $session->token))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('attendance/Public'));
});

test('guest dapat mengisi absensi dan tanda tangan tersimpan', function () {
    Storage::fake('public');

    $form = AttendanceForm::factory()->create([
        'fields' => [
            ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
            ['key' => 'ttd', 'label' => 'Tanda Tangan', 'type' => 'signature', 'required' => true],
        ],
    ]);
    $session = AttendanceSession::factory()->create(['attendance_form_id' => $form->id]);

    $pngDataUrl = 'data:image/png;base64,'.base64_encode('fake-signature-binary');

    post(route('attendance.public.store', $session->token), [
        'answers' => [
            'nama' => 'Budi Santoso',
            'ttd' => $pngDataUrl,
        ],
    ])->assertRedirect();

    $attendance = $session->attendances()->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->name)->toBe('Budi Santoso');
    expect($attendance->signature_path)->not->toBeNull();
    Storage::disk('public')->assertExists($attendance->signature_path);
});

test('absensi dengan field no hp (phone) tervalidasi tanpa error rule', function () {
    $form = AttendanceForm::factory()->create([
        'fields' => [
            ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
            ['key' => 'no_hp', 'label' => 'No. HP', 'type' => 'phone', 'required' => true],
        ],
    ]);
    $session = AttendanceSession::factory()->create(['attendance_form_id' => $form->id]);

    post(route('attendance.public.store', $session->token), [
        'answers' => ['nama' => 'Budi', 'no_hp' => '081234567890'],
    ])->assertRedirect();

    expect($session->attendances()->count())->toBe(1);
});

test('absensi menolak submit bila field wajib kosong', function () {
    $form = AttendanceForm::factory()->create([
        'fields' => [
            ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
        ],
    ]);
    $session = AttendanceSession::factory()->create(['attendance_form_id' => $form->id]);

    post(route('attendance.public.store', $session->token), [
        'answers' => ['nama' => ''],
    ])->assertSessionHasErrors('answers.nama');

    expect($session->attendances()->count())->toBe(0);
});

test('sesi yang ditutup menolak pengisian', function () {
    $session = AttendanceSession::factory()->create(['is_open' => false]);

    post(route('attendance.public.store', $session->token), [
        'answers' => ['nama' => 'Budi'],
    ])->assertForbidden();
});

test('admin dapat export daftar hadir sebagai csv', function () {
    $form = AttendanceForm::factory()->create([
        'fields' => [
            ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
        ],
    ]);
    $session = AttendanceSession::factory()->create(['attendance_form_id' => $form->id]);
    $session->attendances()->create([
        'name' => 'Budi Santoso',
        'data' => ['nama' => 'Budi Santoso'],
        'submitted_at' => now(),
    ]);

    $response = actingAs($this->admin)
        ->get(route('admin.attendance-forms.export', $form));

    $response->assertOk();
    expect($response->streamedContent())->toContain('Budi Santoso');
});

test('admin dapat melihat daftar hadir detail per sesi', function () {
    $form = AttendanceForm::factory()->create([
        'fields' => [
            ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
            ['key' => 'ttd', 'label' => 'Tanda Tangan', 'type' => 'signature', 'required' => true],
        ],
    ]);
    $session = AttendanceSession::factory()->create(['attendance_form_id' => $form->id]);
    $session->attendances()->create([
        'name' => 'Budi Santoso',
        'data' => ['nama' => 'Budi Santoso', 'ttd' => 'signatures/1/abc.png'],
        'signature_path' => 'signatures/1/abc.png',
        'submitted_at' => now(),
    ]);

    actingAs($this->admin)
        ->get(route('admin.attendance-forms.attendances', $form))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/activities/attendance/Attendances')
            ->has('columns', 1) // signature dikeluarkan dari kolom tabel
            ->has('sessions.0.attendances', 1)
            ->where('sessions.0.attendances.0.name', 'Budi Santoso')
            ->where('sessions.0.attendances.0.signature_url', fn ($url) => str_contains($url, 'signatures/1/abc.png'))
        );
});
