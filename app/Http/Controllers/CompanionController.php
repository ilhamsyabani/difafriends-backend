<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanionController extends Controller
{
    public function index(Request $request): Response
    {
        $companions = User::where('role', Roles::Companion->value)
            ->where('is_active', true)
            ->with(['schedules' => fn ($q) => $q->where('is_active', true)])
            ->withCount('bookingsAsTutor')
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2->where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%")
            )
            )
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('companions/Index', [
            'companions' => $companions,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(User $user): Response
    {
        abort_if($user->role !== Roles::Companion, 404);

        $user->load([
            'schedules' => fn ($q) => $q->where('is_active', true)->orderBy('day_of_week'),
            'reviews' => fn ($q) => $q->where('is_published', true)->with('user')->latest()->limit(10),
        ]);

        // Hitung rata-rata rating dari booking reviews
        $avgRating = $user->reviews()->where('is_published', true)->avg('rating') ?? 0;

        return Inertia::render('companions/Show', [
            'companion' => $user,
            'avgRating' => round($avgRating, 1),
        ]);
    }
}
