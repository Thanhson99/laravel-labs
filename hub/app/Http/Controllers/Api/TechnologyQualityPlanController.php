<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyQualityPlanService;
use Illuminate\Http\JsonResponse;

final class TechnologyQualityPlanController extends Controller
{
    /**
     * Return quality gates and verification commands for technology pipelines.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyQualityPlanService $qualityPlan): JsonResponse
    {
        return $this->jsonData($qualityPlan->build($request->sourceFilters(defaultLanguage: 'en')));
    }
}
