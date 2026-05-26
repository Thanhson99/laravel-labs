<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeNextChallengeLabService;
use Illuminate\Contracts\View\View;

final class PracticeNextChallengeLabController extends Controller
{
    /**
     * Show next challenge cards generated from competency maps.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeNextChallengeLabService $challenges): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.next-challenge-lab', [
            'filters' => $filters,
            'lab' => $challenges->build($filters),
        ]);
    }
}
