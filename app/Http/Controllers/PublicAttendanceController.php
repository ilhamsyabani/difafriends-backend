<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceFieldType;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicAttendanceController extends Controller
{
    public function show(AttendanceSession $session): Response
    {
        $session->load('attendanceForm.activity');

        return Inertia::render('attendance/Public', [
            'session' => [
                'token' => $session->token,
                'session_date' => $session->session_date->toDateString(),
                'is_open' => $session->is_open,
                'title' => $session->attendanceForm->title,
                'fields' => $session->attendanceForm->fields,
                'activity' => [
                    'name' => $session->attendanceForm->activity->name,
                    'location' => $session->attendanceForm->activity->location,
                ],
            ],
        ]);
    }

    public function store(Request $request, AttendanceSession $session)
    {
        abort_unless($session->is_open, 403, 'Absensi sudah ditutup.');

        $fields = collect($session->attendanceForm->fields);

        $validated = $request->validate($this->rulesFor($fields), [], $this->attributesFor($fields));

        $data = [];
        $signaturePath = null;

        foreach ($fields as $field) {
            $key = $field['key'];
            $value = $validated['answers'][$key] ?? null;

            if ($field['type'] === AttendanceFieldType::Signature->value) {
                if ($value) {
                    $signaturePath = $this->storeSignature($session, $value);
                    $data[$key] = $signaturePath;
                }

                continue;
            }

            $data[$key] = $value;
        }

        $nameKey = $fields->firstWhere('type', AttendanceFieldType::Text->value)['key'] ?? null;

        $session->attendances()->create([
            'name' => $data[$nameKey] ?? ($data['nama'] ?? 'Peserta'),
            'data' => $data,
            'signature_path' => $signaturePath,
            'ip_address' => $request->ip(),
            'submitted_at' => now(),
        ]);

        return redirect()->route('attendance.public', $session->token)
            ->with('success', 'Terima kasih, absensi Anda sudah tercatat.');
    }

    /**
     * Susun rule validasi dinamis dari konfigurasi field.
     *
     * @param  Collection<int, array<string, mixed>>  $fields
     * @return array<string, mixed>
     */
    private function rulesFor($fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $type = AttendanceFieldType::from($field['type']);
            $required = ! empty($field['required']);
            $key = "answers.{$field['key']}";

            $rule = [$required ? 'required' : 'nullable'];

            if ($type === AttendanceFieldType::Signature) {
                $rule[] = 'string';
                $rule[] = 'starts_with:data:image/';
            } elseif ($type === AttendanceFieldType::Select && ! empty($field['options'])) {
                $rule[] = 'in:'.implode(',', $field['options']);
            } else {
                $rule = array_merge($rule, explode('|', $type->validationRule()));
            }

            $rules[$key] = $rule;
        }

        return $rules;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $fields
     * @return array<string, string>
     */
    private function attributesFor($fields): array
    {
        return $fields->mapWithKeys(
            fn (array $field) => ["answers.{$field['key']}" => $field['label']]
        )->all();
    }

    /**
     * Simpan tanda tangan (data URL base64) sebagai file PNG.
     */
    private function storeSignature(AttendanceSession $session, string $dataUrl): string
    {
        [, $encoded] = explode(',', $dataUrl, 2);
        $binary = base64_decode($encoded);

        $path = "signatures/{$session->id}/".Str::uuid()->toString().'.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
