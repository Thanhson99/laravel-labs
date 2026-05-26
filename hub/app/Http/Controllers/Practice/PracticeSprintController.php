<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSprintService;
use Illuminate\Contracts\View\View;

final class PracticeSprintController extends Controller
{
    /**
     * Show a content-backed sprint of concrete Laravel practice tasks.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSprintService $sprints): View
    {
        $filters = $request->phasedFilters();

        return view('practice.sprint', [
            'filters' => $filters,
            'sprint' => $sprints->build($filters),
        ]);
    }
}
