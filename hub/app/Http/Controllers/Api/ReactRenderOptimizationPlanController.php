<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanReactRenderOptimizationRequest;
use App\Services\Practice\ReactRenderOptimizationPlanService;
use Illuminate\Http\JsonResponse;

final class ReactRenderOptimizationPlanController extends Controller
{
    public function __invoke(PlanReactRenderOptimizationRequest $request, ReactRenderOptimizationPlanService $planner): JsonResponse
    {
        return response()->json([
            'data' => $planner->plan($request->planData()),
        ]);
    }
}
