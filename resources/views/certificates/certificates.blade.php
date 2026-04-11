<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            background: white;
            width: 297mm;
            height: 210mm;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            box-sizing: border-box;
        }
        .border-outer {
            position: absolute;
            inset: 12px;
            border: 3px solid #7C3AED;
        }
        .border-inner {
            position: absolute;
            inset: 18px;
            border: 1px solid #C4B5FD;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #7C3AED;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .tagline {
            font-size: 11px;
            color: #6B7280;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }
        .cert-title {
            font-size: 13px;
            color: #6B7280;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .cert-main {
            font-size: 36px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 6px;
        }
        .cert-sub {
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 20px;
        }
        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #7C3AED;
            margin-bottom: 6px;
            border-bottom: 2px solid #7C3AED;
            padding-bottom: 8px;
            min-width: 300px;
        }
        .completion-text {
            font-size: 13px;
            color: #374151;
            margin: 20px 0 8px;
        }
        .course-name {
            font-size: 20px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 30px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0 60px;
            margin-top: 20px;
        }
        .signature-block {
            text-align: center;
        }
        .signature-line {
            width: 150px;
            border-top: 1px solid #374151;
            margin: 0 auto 6px;
        }
        .signature-name {
            font-size: 12px;
            font-weight: bold;
            color: #1F2937;
        }
        .signature-title {
            font-size: 10px;
            color: #6B7280;
        }
        .cert-number {
            position: absolute;
            bottom: 30px;
            right: 40px;
            font-size: 9px;
            color: #9CA3AF;
        }
        .issued-date {
            position: absolute;
            bottom: 30px;
            left: 40px;
            font-size: 9px;
            color: #9CA3AF;
        }
        .seal {
            position: absolute;
            bottom: 50px;
            right: 150px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #7C3AED;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #7C3AED;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-outer"></div>
        <div class="border-inner"></div>

        <!-- Logo -->
        <div class="logo">DifaFriends</div>
        <div class="tagline">Platform Edukasi Inklusif Indonesia</div>

        <!-- Title -->
        <div class="cert-title">Sertifikat Penyelesaian</div>
        <div class="cert-main">Certificate of Completion</div>
        <div class="cert-sub">Diberikan kepada</div>

        <!-- Nama siswa -->
        <div class="student-name">{{ $certificate->user->first_name }} {{ $certificate->user->last_name }}</div>

        <!-- Teks penyelesaian -->
        <div class="completion-text">
            Telah berhasil menyelesaikan kelas
        </div>
        <div class="course-name">{{ $certificate->course->title }}</div>

        <!-- Footer tanda tangan -->
        <div class="footer">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">Tim DifaFriends</div>
                <div class="signature-title">Platform Edukasi Inklusif</div>
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $certificate->course->instructor->first_name }} {{ $certificate->course->instructor->last_name }}</div>
                <div class="signature-title">Instruktur Kelas</div>
            </div>
        </div>

        <!-- Seal -->
        <div class="seal">
            VERIFIED<br>✓<br>DIFAFRIENDS
        </div>

        <!-- Nomor & tanggal -->
        <div class="cert-number">No: {{ $certificate->certificate_number }}</div>
        <div class="issued-date">Diterbitkan: {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</div>
    </div>
</body>
</html>
