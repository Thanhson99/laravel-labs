<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeChallengeExecutionLabService;
use Illuminate\Contracts\View\View;

final class PracticeChallengeExecutionLabController extends Controller
{
    /**
     * Show executable challenge steps generated from next challenges.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeChallengeExecutionLabService $executions): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.challenge-execution-lab', [
            'filters' => $filters,
            'lab' => $executions->build($filters),
        ]);
    }
}
