<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanAiHallucinationGuardRequest;
use App\Services\Practice\AiHallucinationGuardPlanService;
use Illuminate\Http\JsonResponse;

final class AiHallucinationGuardPlanController extends Controller
{
    /**
     * Return a guardrail plan for reducing AI hallucination in code work.
     */
    public function __invoke(PlanAiHallucinationGuardRequest $request, AiHallucinationGuardPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
