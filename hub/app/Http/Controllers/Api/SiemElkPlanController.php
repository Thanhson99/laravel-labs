<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanSiemElkRequest;
use App\Services\Practice\SiemElkPlanService;
use Illuminate\Http\JsonResponse;

final class SiemElkPlanController extends Controller
{
    /**
     * Return a SIEM and ELK implementation plan.
     */
    public function __invoke(PlanSiemElkRequest $request, SiemElkPlanService $planner): JsonResponse
    {
        return response()->json([
            'data' => $planner->plan($request->planData()),
        ]);
    }
}
