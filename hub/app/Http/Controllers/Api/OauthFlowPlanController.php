<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanOauthFlowRequest;
use App\Services\Practice\OauthFlowPlanService;
use Illuminate\Http\JsonResponse;

final class OauthFlowPlanController extends Controller
{
    /**
     * Return an OAuth flow recommendation for auth security practice.
     */
    public function __invoke(PlanOauthFlowRequest $request, OauthFlowPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
