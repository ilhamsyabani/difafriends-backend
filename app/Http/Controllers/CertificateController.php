<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CertificateController extends Controller
{
    // Download PDF sertifikat
    public function download(Request $request, string $number): Response
    {
        $certificate = Certificate::where('id', $number)
            ->with(['user', 'course.instructor'])
            ->firstOrFail();

        // Hanya pemilik atau admin yang bisa download
        if ($request->user()->id !== $certificate->user_id
            && $request->user()->role !== 'admin') {
            abort(403);
        }

        $pdf = Pdf::loadView('certificates.template', [
            'certificate' => $certificate,
        ])->setPaper('A4', 'landscape');

        return $pdf->download("Sertifikat-{$certificate->certificate_number}.pdf");
    }

    // Verifikasi sertifikat (public)
    public function verify(string $number): \Inertia\Response
    {
        $certificate = Certificate::where('certificate_number', $number)
            ->with(['user', 'course'])
            ->first();

        return Inertia::render('certificates/Verify', [
            'certificate' => $certificate,
            'valid' => (bool) $certificate,
        ]);
    }

    // List sertifikat user
    public function index(Request $request): \Inertia\Response
    {
        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with(['course'])
            ->latest('issued_at')
            ->get();

        return Inertia::render('user/Certificates', [
            'certificates' => $certificates,
        ]);
    }
}
