<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanContainerBindingRequest;
use App\Services\Practice\ContainerBindingPlanService;
use Illuminate\Http\JsonResponse;

final class ContainerBindingPlanController extends Controller
{
    /**
     * Return a service-container binding plan for dependency injection practice.
     */
    public function __invoke(PlanContainerBindingRequest $request, ContainerBindingPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
