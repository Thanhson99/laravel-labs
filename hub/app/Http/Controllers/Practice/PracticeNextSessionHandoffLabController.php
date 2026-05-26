<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeNextSessionHandoffLabService;
use Illuminate\Contracts\View\View;

final class PracticeNextSessionHandoffLabController extends Controller
{
    /**
     * Show next-session handoff cards generated from promotion decisions.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeNextSessionHandoffLabService $handoffs): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.next-session-handoff-lab', [
            'filters' => $filters,
            'lab' => $handoffs->build($filters),
        ]);
    }
}
