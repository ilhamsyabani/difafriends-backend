<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssessmentResultRequest;
use App\Models\Assessment;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Instruments/Index', [
            'hasKesiapan' => Assessment::where('user_id', $user->id)
                ->where('assessment_type', 'kesiapan')
                ->exists(),
            'hasPemilihan' => Assessment::where('user_id', $user->id)
                ->where('assessment_type', 'pemilihan')
                ->exists(),
        ]);
    }

    public function kesiapan(): Response
    {
        $result = Assessment::where('user_id', auth()->id())
            ->where('assessment_type', 'kesiapan')
            ->first();

        return Inertia::render('Instruments/Kesiapan', [
            'result' => $this->resultPayload($result),
            'updatedAt' => $result?->updated_at?->toIso8601String(),
        ]);
    }

    public function pemilihan(): Response
    {
        $result = Assessment::where('user_id', auth()->id())
            ->where('assessment_type', 'pemilihan')
            ->first();

        return Inertia::render('Instruments/Pemilihan', [
            'result' => $this->resultPayload($result),
            'updatedAt' => $result?->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Merge the stored child metadata into the scores payload so the
     * frontend can render the result header alongside the scores.
     *
     * @return array<string, mixed>|null
     */
    private function resultPayload(?Assessment $result): ?array
    {
        if (! $result) {
            return null;
        }

        return [
            ...$result->scores_json,
            'child_name' => $result->child_name,
            'child_age' => $result->child_age,
        ];
    }

    public function store(StoreAssessmentResultRequest $request)
    {
        Assessment::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'assessment_type' => $request->assessment_type,
            ],
            [
                'child_name' => $request->child_name,
                'child_age' => $request->child_age,
                'scores_json' => $request->scores_json,
            ]
        );

        return redirect()->back();
    }

    public function destroy(string $type)
    {
        Assessment::where('user_id', auth()->id())
            ->where('assessment_type', $type)
            ->delete();

        return redirect()->back();
    }
}
