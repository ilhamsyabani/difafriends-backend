<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'DifaFriends',
            'email' => 'admin@difafriends.test',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'role' => Roles::Admin->value,
            'is_active' => true,
            'photo' => 'https://ui-avatars.com/api/?name=Admin+DifaFriends&background=7c3aed&color=fff&size=200',
            'email_verified_at' => now(),
        ]);

        // ── Instructor / Tentor ────────────────────────────
        User::create([
            'first_name' => 'Ahmad',
            'last_name' => 'Fauzi',
            'email' => 'instructor@difafriends.test',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'city' => 'Bandung',
            'country' => 'Indonesia',
            'role' => Roles::Instructor->value,
            'is_active' => true,
            'bio' => 'Instruktur pendidikan khusus dengan pengalaman 8 tahun mendampingi anak-anak autisme dan ADHD. Lulusan S2 Pendidikan Luar Biasa Universitas Pendidikan Indonesia.',
            'photo' => 'https://picsum.photos/seed/instructor1/200/200',
            'email_verified_at' => now(),
        ]);

        // ── Guru Pendamping / Companion ────────────────────
        User::create([
            'first_name' => 'Siti',
            'last_name' => 'Rahayu',
            'email' => 'companion@difafriends.test',
            'password' => Hash::make('password'),
            'phone' => '081234567892',
            'city' => 'Surabaya',
            'country' => 'Indonesia',
            'role' => Roles::Companion->value,
            'is_active' => true,
            'bio' => 'Terapis wicara bersertifikat dengan spesialisasi anak autisme dan down syndrome. Berpengalaman 5 tahun memberikan pendampingan online dan offline.',
            'photo' => 'https://picsum.photos/seed/companion1/200/200',
            'email_verified_at' => now(),
        ]);

        // ── Regular User / Orang Tua ───────────────────────
        User::create([
            'first_name' => 'Rina',
            'last_name' => 'Kusuma',
            'email' => 'user@difafriends.test',
            'password' => Hash::make('password'),
            'phone' => '081234567893',
            'city' => 'Yogyakarta',
            'country' => 'Indonesia',
            'role' => Roles::User->value,
            'is_active' => true,
            'photo' => 'https://picsum.photos/seed/user1/200/200',
            'email_verified_at' => now(),
        ]);

        // ── Instructor tambahan dengan bio ─────────────────
        $instructorProfiles = [
            [
                'first_name' => 'Budi', 'last_name' => 'Santoso',
                'city' => 'Jakarta',
                'bio' => 'Spesialis intervensi perilaku terapan (ABA) dengan pengalaman 6 tahun. Bersertifikat BCBA dan aktif dalam komunitas pendidikan inklusif Indonesia.',
                'photo' => 'https://picsum.photos/seed/instructor2/200/200',
            ],
            [
                'first_name' => 'Dewi', 'last_name' => 'Anggraini',
                'city' => 'Yogyakarta',
                'bio' => 'Instruktur musik adaptif dan seni terapi untuk anak berkebutuhan khusus. Lulusan Seni Musik ISI Yogyakarta dengan pengalaman 4 tahun.',
                'photo' => 'https://picsum.photos/seed/instructor3/200/200',
            ],
            [
                'first_name' => 'Rizky', 'last_name' => 'Hidayat',
                'city' => 'Surabaya',
                'bio' => 'Terapis okupasi dengan fokus pada pengembangan keterampilan motorik halus dan sensorik integrasi. Berpengalaman di berbagai klinik tumbuh kembang.',
                'photo' => 'https://picsum.photos/seed/instructor4/200/200',
            ],
            [
                'first_name' => 'Maya', 'last_name' => 'Putri',
                'city' => 'Bandung',
                'bio' => 'Psikolog anak bersertifikat dengan keahlian asesmen dan intervensi dini. Konsultan di beberapa sekolah inklusi di Jawa Barat.',
                'photo' => 'https://picsum.photos/seed/instructor5/200/200',
            ],
            [
                'first_name' => 'Hendra', 'last_name' => 'Wijaya',
                'city' => 'Semarang',
                'bio' => 'Instruktur matematika adaptif khusus anak disleksia dan diskalkulia. Mengembangkan metode visual-spatial yang terbukti efektif.',
                'photo' => 'https://picsum.photos/seed/instructor6/200/200',
            ],
        ];

        foreach ($instructorProfiles as $profile) {
            User::create(array_merge($profile, [
                'email' => strtolower($profile['first_name'].'.'.$profile['last_name'].'@difafriends.test'),
                'password' => Hash::make('password'),
                'phone' => fake()->numerify('08##########'),
                'country' => 'Indonesia',
                'role' => Roles::Instructor->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]));
        }

        // ── Companion tambahan dengan bio ──────────────────
        $companionProfiles = [
            [
                'first_name' => 'Nurul', 'last_name' => 'Fadilah',
                'city' => 'Jakarta',
                'bio' => 'Shadow teacher berpengalaman 4 tahun mendampingi anak autisme di sekolah inklusi. Terlatih dalam metode TEACCH dan pendekatan naturalistik.',
                'photo' => 'https://picsum.photos/seed/companion2/200/200',
            ],
            [
                'first_name' => 'Agus', 'last_name' => 'Prayitno',
                'city' => 'Malang',
                'bio' => 'Guru pendamping khusus dengan pengalaman mendampingi anak ADHD dan gangguan belajar. Certified dalam manajemen perilaku dan strategi belajar adaptif.',
                'photo' => 'https://picsum.photos/seed/companion3/200/200',
            ],
            [
                'first_name' => 'Fitria', 'last_name' => 'Handayani',
                'city' => 'Bandung',
                'bio' => 'Terapis wicara dan pendamping belajar dengan keahlian khusus dalam komunikasi augmentatif (AAC). 5 tahun pengalaman dengan anak non-verbal.',
                'photo' => 'https://picsum.photos/seed/companion4/200/200',
            ],
            [
                'first_name' => 'Rini', 'last_name' => 'Susanti',
                'city' => 'Surabaya',
                'bio' => 'Spesialis terapi sensorik integrasi dan pendampingan belajar untuk anak dengan gangguan sensorik. Aktif dalam komunitas ABK Jawa Timur.',
                'photo' => 'https://picsum.photos/seed/companion5/200/200',
            ],
            [
                'first_name' => 'Wahyu', 'last_name' => 'Prasetyo',
                'city' => 'Yogyakarta',
                'bio' => 'Guru pendamping dengan latar belakang pendidikan luar biasa. Berpengalaman mendampingi anak down syndrome dalam pembelajaran akademik dan sosial.',
                'photo' => 'https://picsum.photos/seed/companion6/200/200',
            ],
        ];

        foreach ($companionProfiles as $profile) {
            User::create(array_merge($profile, [
                'email' => strtolower($profile['first_name'].'.'.$profile['last_name'].'@difafriends.test'),
                'password' => Hash::make('password'),
                'phone' => fake()->numerify('08##########'),
                'country' => 'Indonesia',
                'role' => Roles::Companion->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]));
        }

        // ── Regular users / Orang tua ──────────────────────
        User::factory(20)->create([
            'role' => Roles::User->value,
            'is_active' => true,
        ]);
    }
}
