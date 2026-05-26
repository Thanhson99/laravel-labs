<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeCompetencyMapLabService;
use Illuminate\Contracts\View\View;

final class PracticeCompetencyMapLabController extends Controller
{
    /**
     * Show competency maps generated from mastery evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeCompetencyMapLabService $maps): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.competency-map-lab', [
            'filters' => $filters,
            'lab' => $maps->build($filters),
        ]);
    }
}
