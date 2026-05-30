<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanReverseProxyFailureRequest;
use App\Services\Practice\ReverseProxyFailurePlanService;
use Illuminate\Http\JsonResponse;

final class ReverseProxyFailurePlanController extends Controller
{
    /**
     * Return a reverse-proxy failure-mode and blast-radius plan.
     */
    public function __invoke(PlanReverseProxyFailureRequest $request, ReverseProxyFailurePlanService $planner): JsonResponse
    {
        return response()->json([
            'data' => $planner->plan($request->planData()),
        ]);
    }
}
