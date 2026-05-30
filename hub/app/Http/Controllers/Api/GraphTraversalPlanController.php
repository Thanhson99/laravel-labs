<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanGraphTraversalRequest;
use App\Services\Practice\GraphTraversalPlanService;
use Illuminate\Http\JsonResponse;

/**
 * Handles BFS/DFS traversal planning API requests.
 */
final class GraphTraversalPlanController extends Controller
{
    /**
     * Return a BFS/DFS traversal plan for algorithm and systems practice.
     */
    public function __invoke(PlanGraphTraversalRequest $request, GraphTraversalPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
