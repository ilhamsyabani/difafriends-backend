<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceFieldType;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AttendanceForm;
use App\Models\AttendanceSession;
use App\Services\QrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceFormController extends Controller
{
    public function create(Activity $activity): Response
    {
        return Inertia::render('admin/activities/attendance/Form', [
            'activity' => $activity,
            'attendanceForm' => null,
            'presetFields' => $this->presetFields(),
            'fieldTypes' => $this->fieldTypeOptions(),
        ]);
    }

    public function store(Request $request, Activity $activity)
    {
        $validated = $this->validateData($request);

        $form = $activity->attendanceForms()->create([
            'title' => $validated['title'],
            'fields' => $validated['fields'],
        ]);

        $this->syncSessions($form, $activity);

        return redirect()->route('admin.attendance-forms.show', $form)
            ->with('success', 'Absensi berhasil dibuat. Link & QR per hari sudah tersedia.');
    }

    public function show(AttendanceForm $attendanceForm, QrCodeGenerator $qr): Response
    {
        $attendanceForm->load([
            'activity',
            'sessions' => function ($query) {
                $query->withCount('attendances')->orderBy('session_date');
            },
        ]);

        $sessions = $attendanceForm->sessions->map(function (AttendanceSession $session) use ($qr) {
            $url = route('attendance.public', $session->token);

            return [
                'id' => $session->id,
                'session_date' => $session->session_date->toDateString(),
                'token' => $session->token,
                'is_open' => $session->is_open,
                'attendances_count' => $session->attendances_count,
                'public_url' => $url,
                'qr' => $qr->dataUri($url, 260),
            ];
        });

        return Inertia::render('admin/activities/attendance/Show', [
            'attendanceForm' => $attendanceForm,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Daftar hadir detail per peserta (dengan preview tanda tangan).
     */
    public function attendances(AttendanceForm $attendanceForm): Response
    {
        $attendanceForm->load([
            'activity',
            'sessions' => function ($query) {
                $query->orderBy('session_date')->with([
                    'attendances' => fn ($q) => $q->latest('submitted_at'),
                ]);
            },
        ]);

        $signatureType = AttendanceFieldType::Signature->value;

        $sessions = $attendanceForm->sessions->map(function (AttendanceSession $session) {
            return [
                'id' => $session->id,
                'session_date' => $session->session_date->toDateString(),
                'attendances' => $session->attendances->map(function ($attendance) {
                    $values = [];
                    $signatureUrl = null;

                    foreach ($attendance->data as $key => $value) {
                        if ($key === 'ttd' || Str::startsWith((string) $value, 'signatures/')) {
                            $signatureUrl = $value ? Storage::disk('public')->url($value) : null;

                            continue;
                        }

                        $values[$key] = $value;
                    }

                    if ($attendance->signature_path) {
                        $signatureUrl = Storage::disk('public')->url($attendance->signature_path);
                    }

                    return [
                        'id' => $attendance->id,
                        'name' => $attendance->name,
                        'values' => $values,
                        'signature_url' => $signatureUrl,
                        'submitted_at' => $attendance->submitted_at?->toDateTimeString(),
                    ];
                }),
            ];
        });

        $columns = collect($attendanceForm->fields)
            ->reject(fn (array $field) => $field['type'] === $signatureType)
            ->map(fn (array $field) => ['key' => $field['key'], 'label' => $field['label']])
            ->values();

        return Inertia::render('admin/activities/attendance/Attendances', [
            'attendanceForm' => $attendanceForm->only(['id', 'title']),
            'activity' => $attendanceForm->activity->only(['id', 'name']),
            'columns' => $columns,
            'sessions' => $sessions,
        ]);
    }

    public function edit(AttendanceForm $attendanceForm): Response
    {
        $attendanceForm->load('activity');

        return Inertia::render('admin/activities/attendance/Form', [
            'activity' => $attendanceForm->activity,
            'attendanceForm' => $attendanceForm,
            'presetFields' => $this->presetFields(),
            'fieldTypes' => $this->fieldTypeOptions(),
        ]);
    }

    public function update(Request $request, AttendanceForm $attendanceForm)
    {
        $validated = $this->validateData($request);

        $attendanceForm->update([
            'title' => $validated['title'],
            'fields' => $validated['fields'],
        ]);

        return redirect()->route('admin.attendance-forms.show', $attendanceForm)
            ->with('success', 'Absensi berhasil diupdate.');
    }

    public function destroy(AttendanceForm $attendanceForm)
    {
        $activity = $attendanceForm->activity;

        $attendanceForm->delete();

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'Absensi berhasil dihapus.');
    }

    public function toggleSession(AttendanceSession $session)
    {
        $session->update(['is_open' => ! $session->is_open]);

        return back()->with('success', $session->is_open ? 'Absensi dibuka.' : 'Absensi ditutup.');
    }

    /**
     * Export daftar hadir seluruh sesi sebagai CSV.
     */
    public function export(AttendanceForm $attendanceForm): StreamedResponse
    {
        $attendanceForm->load(['activity', 'sessions.attendances']);

        $columns = collect($attendanceForm->fields)
            ->reject(fn (array $field) => $field['type'] === AttendanceFieldType::Signature->value);

        $filename = Str::slug($attendanceForm->activity->name.'-'.$attendanceForm->title).'.csv';

        return response()->streamDownload(function () use ($attendanceForm, $columns) {
            $handle = fopen('php://output', 'w');

            $header = array_merge(['Tanggal Sesi'], $columns->pluck('label')->all(), ['Waktu Isi']);
            fputcsv($handle, $header);

            foreach ($attendanceForm->sessions as $session) {
                foreach ($session->attendances as $attendance) {
                    $row = [$session->session_date->toDateString()];

                    foreach ($columns as $field) {
                        $row[] = $attendance->data[$field['key']] ?? '';
                    }

                    $row[] = $attendance->submitted_at?->toDateTimeString();
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Buat 1 sesi per hari sesuai rentang tanggal kegiatan.
     */
    private function syncSessions(AttendanceForm $form, Activity $activity): void
    {
        foreach ($activity->date_range as $date) {
            $form->sessions()->firstOrCreate([
                'session_date' => $date->toDateString(),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'fields' => 'required|array|min:1',
            'fields.*.key' => 'required|string|max:60',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => ['required', Rule::enum(AttendanceFieldType::class)],
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'fields.*.options.*' => 'string|max:255',
        ]);
    }

    /**
     * Field bawaan yang bisa dicentang admin di builder.
     *
     * @return array<int, array<string, mixed>>
     */
    private function presetFields(): array
    {
        return [
            ['key' => 'nama', 'label' => 'Nama Lengkap (untuk sertifikat)', 'type' => 'text', 'required' => true],
            ['key' => 'no_hp', 'label' => 'No. HP', 'type' => 'phone', 'required' => true],
            ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ['key' => 'alamat', 'label' => 'Alamat Domisili', 'type' => 'textarea', 'required' => false],
            ['key' => 'usia', 'label' => 'Usia', 'type' => 'number', 'required' => false],
            ['key' => 'profesi', 'label' => 'Profesi', 'type' => 'text', 'required' => false],
            ['key' => 'instansi', 'label' => 'Instansi', 'type' => 'text', 'required' => false],
            ['key' => 'pendidikan', 'label' => 'Jenjang Pendidikan', 'type' => 'select', 'required' => false, 'options' => ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3']],
            ['key' => 'saran_tema', 'label' => 'Saran Tema Webinar', 'type' => 'textarea', 'required' => false],
            ['key' => 'ttd', 'label' => 'Tanda Tangan', 'type' => 'signature', 'required' => true],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fieldTypeOptions(): array
    {
        return array_map(
            fn (AttendanceFieldType $type) => ['value' => $type->value, 'label' => $type->label()],
            AttendanceFieldType::cases(),
        );
    }
}
