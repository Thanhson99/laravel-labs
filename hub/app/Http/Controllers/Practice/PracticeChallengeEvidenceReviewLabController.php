<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeChallengeEvidenceReviewLabService;
use Illuminate\Contracts\View\View;

final class PracticeChallengeEvidenceReviewLabController extends Controller
{
    /**
     * Show review cards for submitted challenge evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeChallengeEvidenceReviewLabService $reviews): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.challenge-evidence-review-lab', [
            'filters' => $filters,
            'lab' => $reviews->build($filters),
        ]);
    }
}
