<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanDatabaseLockingRequest;
use App\Services\Practice\DatabaseLockingPlanService;
use Illuminate\Http\JsonResponse;

/**
 * Handles database-locking planning API requests.
 */
final class DatabaseLockingPlanController extends Controller
{
    /**
     * Return a transaction-bound database-locking plan.
     */
    public function __invoke(PlanDatabaseLockingRequest $request, DatabaseLockingPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
