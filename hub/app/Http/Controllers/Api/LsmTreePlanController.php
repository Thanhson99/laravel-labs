<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanLsmTreeRequest;
use App\Services\Practice\LsmTreePlanService;
use Illuminate\Http\JsonResponse;

final class LsmTreePlanController extends Controller
{
    /**
     * Return an LSM Tree plan for NoSQL storage-engine practice.
     */
    public function __invoke(PlanLsmTreeRequest $request, LsmTreePlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
