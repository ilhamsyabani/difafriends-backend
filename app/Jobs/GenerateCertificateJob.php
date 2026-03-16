<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $courseId,
    ) {}

    public function handle(): void
    {
        // Cek belum dapat sertifikat sebelumnya
        $exists = Certificate::where('user_id', $this->userId)
                             ->where('course_id', $this->courseId)
                             ->exists();

        if ($exists) return; // sudah punya, skip

        $user   = User::find($this->userId);
        $course = Course::find($this->courseId);

        if (!$user || !$course) return;

        // Generate unique certificate number
        $certNumber = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(6));

        // Simpan record dulu — PDF menyusul
        Certificate::create([
            'user_id'            => $this->userId,
            'course_id'          => $this->courseId,
            'certificate_number' => $certNumber,
            'issued_at'          => now(),
        ]);

        // TODO: Generate PDF via DomPDF — kita kerjakan nanti
    }
}
