<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSessionReplayLabService;
use Illuminate\Contracts\View\View;

final class PracticeSessionReplayLabController extends Controller
{
    /**
     * Show replay rounds generated from next-session handoffs.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSessionReplayLabService $replays): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.session-replay-lab', [
            'filters' => $filters,
            'lab' => $replays->build($filters),
        ]);
    }
}
