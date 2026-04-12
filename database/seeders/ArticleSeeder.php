<?php

namespace Database\Seeders;

use App\Enums\ArticlesStatus;
use App\Enums\Roles;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', Roles::Admin->value)->first()
            ?? User::where('role', Roles::Instructor->value)->first();

        $articles = [
            [
                'title' => 'Mengenal Terapi Wicara untuk Anak Berkebutuhan Khusus',
                'slug' => 'mengenal-terapi-wicara-anak-berkebutuhan-khusus',
                'thumbnail' => 'https://picsum.photos/seed/article1/800/450',
                'content' => '<p>Terapi wicara merupakan salah satu intervensi penting bagi anak-anak yang mengalami gangguan komunikasi. Program ini dirancang untuk membantu anak mengembangkan kemampuan berbicara, memahami bahasa, dan berkomunikasi secara efektif.</p><p>Banyak orang tua yang belum memahami kapan waktu yang tepat untuk membawa anak ke terapis wicara. Secara umum, intervensi dini sangat dianjurkan — semakin cepat anak mendapatkan penanganan, semakin baik hasilnya.</p><p>Terapi wicara tidak hanya mencakup latihan berbicara, tetapi juga meliputi aspek kognitif seperti pemahaman instruksi, kemampuan bernarasi, dan interaksi sosial melalui bahasa.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Tips Mendampingi Anak Autisme Belajar di Rumah',
                'slug' => 'tips-mendampingi-anak-autisme-belajar-di-rumah',
                'thumbnail' => 'https://picsum.photos/seed/article2/800/450',
                'content' => '<p>Mendampingi anak autisme belajar di rumah memerlukan kesabaran ekstra dan strategi yang tepat. Setiap anak autisme memiliki kebutuhan unik yang berbeda-beda, sehingga pendekatan yang digunakan pun perlu disesuaikan.</p><p>Salah satu kunci keberhasilan adalah konsistensi rutinitas. Anak autisme umumnya merasa lebih nyaman dan aman dengan jadwal yang teratur dan dapat diprediksi. Ciptakan lingkungan belajar yang minim gangguan sensorik seperti suara keras atau cahaya yang terlalu terang.</p><p>Gunakan media visual seperti gambar, kartu flash, atau video pendek untuk membantu anak memahami konsep abstrak. Pendekatan berbasis kekuatan (strength-based approach) juga terbukti efektif — fokuslah pada apa yang bisa dilakukan anak, bukan pada keterbatasannya.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Peran Guru Pendamping dalam Pendidikan Inklusif',
                'slug' => 'peran-guru-pendamping-pendidikan-inklusif',
                'thumbnail' => 'https://picsum.photos/seed/article3/800/450',
                'content' => '<p>Pendidikan inklusif adalah pendekatan yang memungkinkan anak berkebutuhan khusus belajar bersama dengan anak-anak pada umumnya di sekolah reguler. Dalam sistem ini, guru pendamping atau shadow teacher memegang peran yang sangat vital.</p><p>Guru pendamping bertugas membantu anak memahami materi pelajaran, menerjemahkan instruksi guru kelas, serta memfasilitasi interaksi sosial dengan teman-teman sekelas. Mereka juga berkolaborasi erat dengan orang tua dan tim terapis untuk merancang program pembelajaran yang komprehensif.</p><p>Kompetensi yang dibutuhkan seorang guru pendamping meliputi pemahaman tentang berbagai kondisi disabilitas, kemampuan komunikasi yang baik, serta keterampilan manajemen perilaku yang konstruktif.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Strategi Komunikasi Augmentatif untuk Anak Non-Verbal',
                'slug' => 'strategi-komunikasi-augmentatif-anak-non-verbal',
                'thumbnail' => 'https://picsum.photos/seed/article4/800/450',
                'content' => '<p>Komunikasi augmentatif dan alternatif (AAC) adalah sistem yang membantu individu yang memiliki kesulitan dalam berbicara untuk tetap dapat berkomunikasi secara efektif. Sistem ini mencakup berbagai alat dan strategi, mulai dari gambar sederhana hingga perangkat teknologi canggih.</p><p>Bagi anak non-verbal atau yang memiliki keterbatasan bicara, AAC dapat menjadi jembatan yang memungkinkan mereka mengekspresikan kebutuhan, keinginan, dan perasaan mereka kepada orang-orang di sekitar mereka.</p><p>Beberapa pendekatan AAC yang umum digunakan antara lain PECS (Picture Exchange Communication System), papan komunikasi bergambar, dan aplikasi berbasis tablet yang dirancang khusus untuk membantu komunikasi.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Memahami Sensory Processing Disorder pada Anak',
                'slug' => 'memahami-sensory-processing-disorder-anak',
                'thumbnail' => 'https://picsum.photos/seed/article5/800/450',
                'content' => '<p>Sensory Processing Disorder (SPD) adalah kondisi di mana otak mengalami kesulitan menerima dan merespons informasi yang datang melalui indera. Anak dengan SPD mungkin terlalu sensitif (hypersensitive) atau kurang sensitif (hyposensitive) terhadap rangsangan sensorik.</p><p>Gejala SPD dapat bervariasi dari satu anak ke anak lainnya. Ada yang tidak tahan dengan tekstur pakaian tertentu, ada yang sangat terganggu dengan suara bising, dan ada pula yang terus-menerus mencari stimulasi sensorik dengan cara berputar atau melompat.</p><p>Terapi integrasi sensorik yang dilakukan oleh terapis okupasi terlatih dapat membantu anak mengembangkan toleransi terhadap berbagai rangsangan sensorik dan meningkatkan fungsi keseharian mereka.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Cara Memilih Kelas Online yang Tepat untuk Anak Berkebutuhan Khusus',
                'slug' => 'cara-memilih-kelas-online-anak-berkebutuhan-khusus',
                'thumbnail' => 'https://picsum.photos/seed/article6/800/450',
                'content' => '<p>Dengan semakin berkembangnya platform pembelajaran online, kini semakin banyak pilihan kelas yang tersedia untuk anak berkebutuhan khusus. Namun, tidak semua kelas cocok untuk setiap anak. Memilih kelas yang tepat memerlukan pertimbangan yang matang.</p><p>Pertama, pastikan instruktur memiliki latar belakang dan pengalaman dalam pendidikan khusus atau terapi. Sertifikasi dan pengalaman kerja merupakan indikator penting kompetensi mereka.</p><p>Kedua, perhatikan rasio instruktur-siswa. Kelas dengan jumlah siswa yang lebih kecil umumnya memberikan perhatian yang lebih personal dan efektif untuk anak berkebutuhan khusus.</p><p>Ketiga, cari platform yang menyediakan materi dalam berbagai format — video, audio, teks, dan gambar — untuk mengakomodasi berbagai gaya belajar.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Pentingnya Kolaborasi Orang Tua dan Terapis dalam Tumbuh Kembang Anak',
                'slug' => 'kolaborasi-orang-tua-terapis-tumbuh-kembang-anak',
                'thumbnail' => 'https://picsum.photos/seed/article7/800/450',
                'content' => '<p>Kolaborasi yang erat antara orang tua dan tim terapis merupakan salah satu faktor terpenting dalam keberhasilan program intervensi untuk anak berkebutuhan khusus. Tanpa keterlibatan aktif orang tua, kemajuan yang dicapai selama sesi terapi dapat sulit untuk dipertahankan di lingkungan rumah.</p><p>Orang tua perlu memahami tujuan terapi, teknik yang digunakan, dan cara mengimplementasikannya dalam aktivitas sehari-hari. Komunikasi rutin antara orang tua dan terapis sangat penting untuk memantau perkembangan anak dan menyesuaikan program jika diperlukan.</p>',
                'status' => ArticlesStatus::Published,
            ],
            [
                'title' => 'Teknologi Assistive: Inovasi untuk Mendukung Belajar Anak Difabel',
                'slug' => 'teknologi-assistive-inovasi-belajar-anak-difabel',
                'thumbnail' => 'https://picsum.photos/seed/article8/800/450',
                'content' => '<p>Perkembangan teknologi telah membuka peluang baru yang luar biasa bagi anak-anak penyandang disabilitas untuk belajar dan berpartisipasi dalam kehidupan sehari-hari. Teknologi assistive mencakup berbagai perangkat keras dan perangkat lunak yang dirancang untuk membantu individu dengan disabilitas.</p><p>Untuk anak dengan gangguan penglihatan, tersedia pembaca layar (screen reader) dan buku braille digital. Anak dengan gangguan pendengaran dapat memanfaatkan captioning otomatis dan aplikasi bahasa isyarat. Sementara anak dengan keterbatasan gerak dapat menggunakan perangkat input alternatif seperti eye-tracking atau switch access.</p>',
                'status' => ArticlesStatus::Published,
            ],
        ];

        foreach ($articles as $data) {
            Article::create(array_merge($data, [
                'author_id' => $author->id,
                'created_at' => now()->subDays(rand(1, 180)),
            ]));
        }
    }
}
