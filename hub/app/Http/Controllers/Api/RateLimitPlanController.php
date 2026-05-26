<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanRateLimitRequest;
use App\Services\Practice\RateLimitPlanService;
use Illuminate\Http\JsonResponse;

final class RateLimitPlanController extends Controller
{
    /**
     * Return a rate-limit plan for API security practice.
     */
    public function __invoke(PlanRateLimitRequest $request, RateLimitPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
