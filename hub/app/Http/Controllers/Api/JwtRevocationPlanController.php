<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanJwtRevocationRequest;
use App\Services\Practice\JwtRevocationPlanService;
use Illuminate\Http\JsonResponse;

final class JwtRevocationPlanController extends Controller
{
    /**
     * Return a JWT revocation plan for API and database security practice.
     */
    public function __invoke(PlanJwtRevocationRequest $request, JwtRevocationPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
