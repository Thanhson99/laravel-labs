<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanAsyncJobRequest;
use App\Services\Practice\AsyncJobPlanService;
use Illuminate\Http\JsonResponse;

final class AsyncJobPlanController extends Controller
{
    /**
     * Return an async job implementation plan for queue practice.
     */
    public function __invoke(PlanAsyncJobRequest $request, AsyncJobPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
