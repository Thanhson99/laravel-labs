<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyRemediationPlanService;
use Illuminate\Http\JsonResponse;

final class TechnologyRemediationPlanController extends Controller
{
    /**
     * Return remediation tasks for one inferred technology assessment.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyRemediationPlanService $plans
    ): JsonResponse {
        return $this->jsonData($plans->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
