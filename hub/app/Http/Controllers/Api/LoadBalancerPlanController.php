<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanLoadBalancerRequest;
use App\Services\Practice\LoadBalancerPlanService;
use Illuminate\Http\JsonResponse;

final class LoadBalancerPlanController extends Controller
{
    /**
     * Return a load-balancer plan for system-design interview practice.
     */
    public function __invoke(PlanLoadBalancerRequest $request, LoadBalancerPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
