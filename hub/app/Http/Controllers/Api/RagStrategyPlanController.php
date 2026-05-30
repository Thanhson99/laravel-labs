<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanRagStrategyRequest;
use App\Services\Practice\RagStrategyPlanService;
use Illuminate\Http\JsonResponse;

final class RagStrategyPlanController extends Controller
{
    /**
     * Return a RAG strategy plan for classic, graph, and agentic RAG practice.
     */
    public function __invoke(PlanRagStrategyRequest $request, RagStrategyPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
