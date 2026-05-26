<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanAuthorizationPolicyRequest;
use App\Services\Practice\AuthorizationPolicyPlanService;
use Illuminate\Http\JsonResponse;

final class AuthorizationPolicyPlanController extends Controller
{
    /**
     * Return an authorization policy plan for access-control practice.
     */
    public function __invoke(PlanAuthorizationPolicyRequest $request, AuthorizationPolicyPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
