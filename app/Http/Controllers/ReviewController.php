<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:course,booking,companion',
            'id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 1. Tentukan target Model berdasarkan tipe
        if ($validated['type'] === 'course') {
            $model = Course::findOrFail($validated['id']);
            $modelClass = Course::class;

        } elseif ($validated['type'] === 'booking') {
            $model = Booking::findOrFail($validated['id']);
            $modelClass = Booking::class;

        } elseif ($validated['type'] === 'companion') {
            $model = User::where('role', Roles::Companion->value)->findOrFail($validated['id']);
            $modelClass = User::class;
        }

        // 2. Cek agar user tidak spam review berkali-kali untuk target yang sama
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('reviewable_type', $modelClass)
            ->where('reviewable_id', $model->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah pernah memberikan ulasan untuk item ini.');
        }

        // 3. Simpan Review
        Review::create([
            'user_id' => $request->user()->id,
            'reviewable_type' => $modelClass,
            'reviewable_id' => $model->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_published' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }
}
