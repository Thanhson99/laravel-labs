<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeChallengePromotionLabService;
use Illuminate\Contracts\View\View;

final class PracticeChallengePromotionLabController extends Controller
{
    /**
     * Show promotion decisions generated from reviewed challenge evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeChallengePromotionLabService $promotions): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.challenge-promotion-lab', [
            'filters' => $filters,
            'lab' => $promotions->build($filters),
        ]);
    }
}
