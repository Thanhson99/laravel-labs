<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanSystemDesignTradeoffRequest;
use App\Services\Practice\SystemDesignTradeoffPlanService;
use Illuminate\Http\JsonResponse;

final class SystemDesignTradeoffPlanController extends Controller
{
    /**
     * Return a system-design tradeoff plan for interview practice.
     */
    public function __invoke(PlanSystemDesignTradeoffRequest $request, SystemDesignTradeoffPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
