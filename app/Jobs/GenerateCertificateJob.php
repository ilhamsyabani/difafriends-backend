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

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public Course $course,
    ) {}

    public function handle(): void
    {
        // Cek sudah ada sertifikat
        $exists = Certificate::where('user_id', $this->user->id)
            ->where('course_id', $this->course->id)
            ->exists();

        if ($exists) return;

        Certificate::create([
            'user_id'            => $this->user->id,
            'course_id'          => $this->course->id,
            'certificate_number' => Certificate::generateNumber(),
            'issued_at'          => now(),
        ]);
    }
}
