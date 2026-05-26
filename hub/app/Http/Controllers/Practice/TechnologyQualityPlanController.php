<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyQualityPlanService;
use Illuminate\Contracts\View\View;

final class TechnologyQualityPlanController extends Controller
{
    /**
     * Show quality gates and verification commands for technology pipelines.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyQualityPlanService $qualityPlan): View
    {
        $filters = $request->sourceFilters(defaultLanguage: 'en');

        return view('practice.technology-quality-plan', [
            'filters' => $filters,
            'qualityPlan' => $qualityPlan->build($filters),
        ]);
    }
}
