<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $categories = [
            [
                'name'        => 'Terapi',
                'description' => 'Program terapi untuk anak berkebutuhan khusus',
                'children'    => [
                    'Terapi Wicara',
                    'Terapi Sensori Integrasi',
                    'Terapi Perilaku (ABA)',
                    'Terapi Okupasi',
                ],
            ],
            [
                'name'        => 'Akademik',
                'description' => 'Pembelajaran akademik dasar yang disesuaikan',
                'children'    => [
                    'Membaca & Menulis',
                    'Matematika Dasar',
                    'Sains Dasar',
                ],
            ],
            [
                'name'        => 'Sosial & Emosional',
                'description' => 'Pengembangan kemampuan sosial dan emosional',
                'children'    => [
                    'Keterampilan Sosial',
                    'Regulasi Emosi',
                    'Komunikasi Non-Verbal',
                ],
            ],
            [
                'name'        => 'Motorik',
                'description' => 'Pengembangan kemampuan motorik halus dan kasar',
                'children'    => [
                    'Motorik Halus',
                    'Motorik Kasar',
                ],
            ],
        ];

        foreach ($categories as $cat) {
            // Buat parent category
            $parent = Category::create([
                'name'        => $cat['name'],
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['description'],
                'is_active'   => true,
                'sort_order'  => 0,
            ]);

            // Buat child categories
            foreach ($cat['children'] as $index => $childName) {
                Category::create([
                    'parent_id'  => $parent->id,
                    'name'       => $childName,
                    'slug'       => Str::slug($childName),
                    'is_active'  => true,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
