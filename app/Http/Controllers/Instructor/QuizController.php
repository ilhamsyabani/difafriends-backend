<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function create(Request $request, Course $course, CourseSection $section): Response
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        // Cek sudah ada quiz di section ini
        $existingQuiz = $section->quiz;

        if ($existingQuiz) {
            return redirect()->route(
                'instructor.courses.sections.quiz.edit',
                [$course, $section, $existingQuiz]
            );
        }

        return Inertia::render('instructor/quiz/Form', [
            'course'  => $course,
            'section' => $section,
            'quiz'    => null,
        ]);
    }

    public function store(Request $request, Course $course, CourseSection $section)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'is_required'   => 'boolean',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $quiz = Quiz::create([
            ...$validated,
            'course_id'  => $course->id,
            'section_id' => $section->id,
        ]);

        return redirect()->route(
            'instructor.courses.sections.quiz.edit',
            [$course, $section, $quiz]
        )->with('success', 'Kuis berhasil dibuat. Tambahkan soal sekarang!');
    }

    public function edit(Request $request, Course $course, CourseSection $section, Quiz $quiz): Response
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $quiz->load(['questions.options']);

        return Inertia::render('instructor/quiz/Form', [
            'course'  => $course,
            'section' => $section,
            'quiz'    => $quiz,
        ]);
    }

    public function update(Request $request, Course $course, CourseSection $section, Quiz $quiz)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'is_required'   => 'boolean',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $quiz->update($validated);

        return back()->with('success', 'Kuis berhasil diupdate.');
    }

    public function destroy(Request $request, Course $course, CourseSection $section, Quiz $quiz)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $quiz->delete();

        return back()->with('success', 'Kuis berhasil dihapus.');
    }
}
