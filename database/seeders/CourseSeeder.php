<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseGoal;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Buat 10 course published
        Course::factory(10)->published()->create()->each(function ($course) {
            $this->seedCourseContent($course);
        });

        // Buat 3 course featured
        Course::factory(3)->featured()->create()->each(function ($course) {
            $this->seedCourseContent($course);
        });

        // Buat 2 course draft
        Course::factory(2)->draft()->create();
    }

    private function seedCourseContent($course): void
    {
        // Goals
        CourseGoal::insert(
            collect(range(1, rand(3, 5)))->map(fn () => [
                'course_id' => $course->id,
                'goal_name' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        // Sections & Lectures
        foreach (range(1, rand(2, 4)) as $sectionOrder) {
            $section = CourseSection::create([
                'course_id' => $course->id,
                'title' => 'Bagian '.$sectionOrder.': '.fake()->words(3, true),
                'sort_order' => $sectionOrder,
            ]);

            foreach (range(1, rand(3, 6)) as $lectureOrder) {
                CourseLecture::create([
                    'course_id' => $course->id,
                    'section_id' => $section->id,
                    'title' => fake()->sentence(4),
                    'type' => 'video',
                    'url' => 'https://www.youtube.com/watch?v='.fake()->regexify('[A-Za-z0-9]{11}'),
                    'video_duration' => fake()->numberBetween(300, 3600),
                    'is_free_preview' => $lectureOrder === 1,
                    'sort_order' => $lectureOrder,
                ]);
            }
        }
    }
}
