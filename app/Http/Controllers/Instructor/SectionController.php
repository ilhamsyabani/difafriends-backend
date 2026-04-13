<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $course->sections()->create($validated);

        return back()->with('success', 'Bagian berhasil ditambahkan.');
    }

    public function update(Request $request, Course $course, CourseSection $section)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
        ]);

        $section->update($validated);

        return back()->with('success', 'Bagian berhasil diupdate.');
    }

    public function destroy(Request $request, Course $course, CourseSection $section)
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        $section->delete();

        return back()->with('success', 'Bagian berhasil dihapus.');
    }
}
