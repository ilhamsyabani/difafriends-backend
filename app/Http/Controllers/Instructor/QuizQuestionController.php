<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function store(
        Request $request,
        Course $course,
        CourseSection $section,
        Quiz $quiz
    ) {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'sort_order' => 'integer|min:0',
            // Untuk PG: options wajib ada minimal 2 + 1 yang benar
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.is_correct' => 'required|boolean',
        ]);

        // Validasi: PG harus punya tepat 1 jawaban benar
        if ($validated['type'] === 'multiple_choice') {
            $correctCount = collect($validated['options'])
                ->where('is_correct', true)->count();

            if ($correctCount !== 1) {
                return back()->withErrors([
                    'options' => 'Pilihan ganda harus memiliki tepat 1 jawaban benar.',
                ]);
            }
        }

        $question = $quiz->questions()->create([
            'type' => $validated['type'],
            'question' => $validated['question'],
            'points' => $validated['points'],
            'sort_order' => $validated['sort_order'] ?? $quiz->questions()->count(),
        ]);

        // Buat options untuk PG
        if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
            foreach ($validated['options'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => $opt['is_correct'],
                ]);
            }
        }

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        Course $course,
        CourseSection $section,
        Quiz $quiz,
        QuizQuestion $question
    ) {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'question' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'sort_order' => 'integer|min:0',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $question->update([
            'question' => $validated['question'],
            'points' => $validated['points'],
            'sort_order' => $validated['sort_order'] ?? $question->sort_order,
        ]);

        // Update options untuk PG
        if ($question->isMultipleChoice() && isset($validated['options'])) {
            $question->options()->delete();
            foreach ($validated['options'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => $opt['is_correct'],
                ]);
            }
        }

        return back()->with('success', 'Soal berhasil diupdate.');
    }

    public function destroy(
        Request $request,
        Course $course,
        CourseSection $section,
        Quiz $quiz,
        QuizQuestion $question
    ) {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
