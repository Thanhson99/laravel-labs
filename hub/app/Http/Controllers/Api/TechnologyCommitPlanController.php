<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyCommitPlanService;
use Illuminate\Http\JsonResponse;

final class TechnologyCommitPlanController extends Controller
{
    /**
     * Return commit-ready artifacts for one technology implementation lab.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyCommitPlanService $plans
    ): JsonResponse {
        return $this->jsonData($plans->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
