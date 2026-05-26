<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyRemediationPlanService;
use Illuminate\Contracts\View\View;

final class TechnologyRemediationPlanController extends Controller
{
    /**
     * Show remediation tasks for one inferred technology assessment.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyRemediationPlanService $plans
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-remediation-plan', [
            'plan' => $plans->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
