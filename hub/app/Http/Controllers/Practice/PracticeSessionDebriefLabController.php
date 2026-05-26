<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSessionDebriefLabService;
use Illuminate\Contracts\View\View;

final class PracticeSessionDebriefLabController extends Controller
{
    /**
     * Show debrief cards generated from session replay rounds.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSessionDebriefLabService $debriefs): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.session-debrief-lab', [
            'filters' => $filters,
            'lab' => $debriefs->build($filters),
        ]);
    }
}
