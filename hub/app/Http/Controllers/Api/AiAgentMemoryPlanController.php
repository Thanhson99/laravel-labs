<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanAiAgentMemoryRequest;
use App\Services\Practice\AiAgentMemoryPlanService;
use Illuminate\Http\JsonResponse;

/**
 * Handle runnable AI agent memory planning requests.
 */
final class AiAgentMemoryPlanController extends Controller
{
    /**
     * Return an AI agent memory plan for developer-agent practice.
     */
    public function __invoke(PlanAiAgentMemoryRequest $request, AiAgentMemoryPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
