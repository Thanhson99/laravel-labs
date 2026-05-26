<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyCommitPlanService;
use Illuminate\Contracts\View\View;

final class TechnologyCommitPlanController extends Controller
{
    /**
     * Show commit-ready artifacts for one technology implementation lab.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyCommitPlanService $plans
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-commit-plan', [
            'plan' => $plans->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
