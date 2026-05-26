<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanEventListenerRequest;
use App\Services\Practice\EventListenerPlanService;
use Illuminate\Http\JsonResponse;

final class EventListenerPlanController extends Controller
{
    /**
     * Return an event/listener implementation plan for side-effect practice.
     */
    public function __invoke(PlanEventListenerRequest $request, EventListenerPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
