<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeBugFixLabService;
use Illuminate\Contracts\View\View;

final class PracticeBugFixLabController extends Controller
{
    /**
     * Show bug-fix drills generated from live coding rounds.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeBugFixLabService $drills): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.bug-fix-lab', [
            'filters' => $filters,
            'lab' => $drills->build($filters),
        ]);
    }
}
