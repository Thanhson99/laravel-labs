<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanJwtTokenStorageRequest;
use App\Services\Practice\JwtTokenStoragePlanService;
use Illuminate\Http\JsonResponse;

final class JwtTokenStoragePlanController extends Controller
{
    /**
     * Return a JWT token-storage recommendation for auth security practice.
     */
    public function __invoke(PlanJwtTokenStorageRequest $request, JwtTokenStoragePlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
