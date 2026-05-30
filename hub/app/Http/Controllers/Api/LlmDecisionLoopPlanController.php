<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanLlmDecisionLoopRequest;
use App\Services\Practice\LlmDecisionLoopPlanService;
use Illuminate\Http\JsonResponse;

final class LlmDecisionLoopPlanController extends Controller
{
    /**
     * Return an LLM decision-loop plan for AI foundations practice.
     */
    public function __invoke(PlanLlmDecisionLoopRequest $request, LlmDecisionLoopPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
