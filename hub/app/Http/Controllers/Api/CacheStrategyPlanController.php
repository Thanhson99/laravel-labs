<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanCacheStrategyRequest;
use App\Services\Practice\CacheStrategyPlanService;
use Illuminate\Http\JsonResponse;

final class CacheStrategyPlanController extends Controller
{
    /**
     * Return a cache strategy plan for performance practice.
     */
    public function __invoke(PlanCacheStrategyRequest $request, CacheStrategyPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
