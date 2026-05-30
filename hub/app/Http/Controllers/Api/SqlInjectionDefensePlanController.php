<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanSqlInjectionDefenseRequest;
use App\Services\Practice\SqlInjectionDefensePlanService;
use Illuminate\Http\JsonResponse;

final class SqlInjectionDefensePlanController extends Controller
{
    public function __invoke(PlanSqlInjectionDefenseRequest $request, SqlInjectionDefensePlanService $planner): JsonResponse
    {
        return response()->json([
            'data' => $planner->plan($request->planData()),
        ]);
    }
}
