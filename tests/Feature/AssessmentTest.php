<?php

use App\Models\Assessment;
use App\Models\User;

function kesiapanScores(): array
{
    return [
        'aspects' => [
            'a' => ['label' => 'Kelekatan', 'subtotal' => 12, 'max' => 15],
        ],
        'total' => 80,
        'max_total' => 99,
        'kategori' => 'Sangat Siap Sekolah',
    ];
}

function pemilihanScores(): array
{
    return [
        'child_profile' => ['minat' => ['suka_membaca'], 'kebutuhan' => ['lingkungan_tenang']],
        'schools' => [
            'school_1' => [
                'name' => 'SD Harapan',
                'aspects' => [
                    'b1' => ['label' => 'Kesesuaian', 'subtotal' => 18, 'max' => 20],
                ],
                'total' => 120,
                'max_total' => 140,
                'kategori' => 'Sangat Direkomendasikan',
            ],
        ],
    ];
}

test('index menampilkan status pengisian instrumen', function () {
    $user = User::factory()->create();

    Assessment::create([
        'user_id' => $user->id,
        'assessment_type' => 'kesiapan',
        'child_name' => 'Budi',
        'child_age' => '6 tahun',
        'scores_json' => kesiapanScores(),
    ]);

    $this->actingAs($user)
        ->get(route('assessment.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Instruments/Index')
            ->where('hasKesiapan', true)
            ->where('hasPemilihan', false)
        );
});

test('halaman kesiapan menyertakan nama dan usia anak pada result', function () {
    $user = User::factory()->create();

    Assessment::create([
        'user_id' => $user->id,
        'assessment_type' => 'kesiapan',
        'child_name' => 'Budi',
        'child_age' => '6 tahun',
        'scores_json' => kesiapanScores(),
    ]);

    $this->actingAs($user)
        ->get(route('assessment.kesiapan'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Instruments/Kesiapan')
            ->where('result.child_name', 'Budi')
            ->where('result.child_age', '6 tahun')
            ->where('result.total', 80)
            ->whereNot('updatedAt', null)
        );
});

test('menyimpan hasil kesiapan membuat record', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('assessment.results'), [
            'assessment_type' => 'kesiapan',
            'child_name' => 'Budi',
            'child_age' => '6 tahun',
            'scores_json' => kesiapanScores(),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('assessments', [
        'user_id' => $user->id,
        'assessment_type' => 'kesiapan',
        'child_name' => 'Budi',
    ]);
});

test('menyimpan hasil pemilihan membuat record', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('assessment.results'), [
            'assessment_type' => 'pemilihan',
            'child_name' => 'Budi',
            'child_age' => '6 tahun',
            'scores_json' => pemilihanScores(),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('assessments', [
        'user_id' => $user->id,
        'assessment_type' => 'pemilihan',
    ]);
});

test('menyimpan hasil memperbarui record yang sudah ada (tidak duplikat)', function () {
    $user = User::factory()->create();

    foreach (['Budi', 'Sari'] as $name) {
        $this->actingAs($user)->post(route('assessment.results'), [
            'assessment_type' => 'kesiapan',
            'child_name' => $name,
            'child_age' => '6 tahun',
            'scores_json' => kesiapanScores(),
        ])->assertRedirect();
    }

    expect(Assessment::where('user_id', $user->id)->count())->toBe(1);
    $this->assertDatabaseHas('assessments', ['user_id' => $user->id, 'child_name' => 'Sari']);
});

test('validasi gagal ketika scores_json kesiapan tidak lengkap', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('assessment.results'), [
            'assessment_type' => 'kesiapan',
            'child_name' => 'Budi',
            'child_age' => '6 tahun',
            'scores_json' => ['total' => 80], // kategori & aspects hilang
        ])
        ->assertSessionHasErrors(['scores_json.aspects', 'scores_json.kategori']);
});

test('menghapus hasil menghilangkan record sesuai tipe', function () {
    $user = User::factory()->create();

    Assessment::create([
        'user_id' => $user->id,
        'assessment_type' => 'kesiapan',
        'child_name' => 'Budi',
        'child_age' => '6 tahun',
        'scores_json' => kesiapanScores(),
    ]);

    $this->actingAs($user)
        ->delete(route('assessment.destroy', ['type' => 'kesiapan']))
        ->assertRedirect();

    $this->assertDatabaseMissing('assessments', [
        'user_id' => $user->id,
        'assessment_type' => 'kesiapan',
    ]);
});

test('tamu tidak bisa menyimpan hasil', function () {
    $this->post(route('assessment.results'), [
        'assessment_type' => 'kesiapan',
        'child_name' => 'Budi',
        'child_age' => '6 tahun',
        'scores_json' => kesiapanScores(),
    ])->assertRedirect(route('login'));
});
