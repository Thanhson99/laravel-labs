<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanCsrfProtectionRequest;
use App\Services\Practice\CsrfProtectionPlanService;
use Illuminate\Http\JsonResponse;

final class CsrfProtectionPlanController extends Controller
{
    public function __invoke(PlanCsrfProtectionRequest $request, CsrfProtectionPlanService $planner): JsonResponse
    {
        return response()->json([
            'data' => $planner->plan($request->planData()),
        ]);
    }
}
