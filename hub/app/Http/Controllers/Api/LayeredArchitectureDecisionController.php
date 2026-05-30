<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanLayeredArchitectureDecisionRequest;
use App\Services\Practice\LayeredArchitectureDecisionService;
use Illuminate\Http\JsonResponse;

final class LayeredArchitectureDecisionController extends Controller
{
    /**
     * Return a layered architecture decision plan.
     */
    public function __invoke(PlanLayeredArchitectureDecisionRequest $request, LayeredArchitectureDecisionService $planner): JsonResponse
    {
        return $this->jsonData($planner->decide($request->planData()));
    }
}
