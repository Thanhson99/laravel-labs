<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanRestfulApiNamingRequest;
use App\Services\Practice\RestfulApiNamingPlanService;
use Illuminate\Http\JsonResponse;

final class RestfulApiNamingPlanController extends Controller
{
    /**
     * Return a RESTful API endpoint naming plan.
     */
    public function __invoke(PlanRestfulApiNamingRequest $request, RestfulApiNamingPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
