<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    public function store(Request $request, Course $course, CourseSection $section)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,text,quiz',
            'url' => 'nullable|url',
            'content' => 'nullable|string',
            'video_duration' => 'integer|min:0',
            'is_free_preview' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['course_id'] = $course->id;
        $section->lectures()->create($validated);

        return back()->with('success', 'Lecture berhasil ditambahkan.');
    }

    public function update(Request $request, Course $course, CourseSection $section, CourseLecture $lecture)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,text,quiz',
            'url' => 'nullable|url',
            'content' => 'nullable|string',
            'video_duration' => 'integer|min:0',
            'is_free_preview' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $lecture->update($validated);

        return back()->with('success', 'Lecture berhasil diupdate.');
    }

    public function destroy(Request $request, Course $course, CourseSection $section, CourseLecture $lecture)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $lecture->delete();

        return back()->with('success', 'Lecture berhasil dihapus.');
    }
}
