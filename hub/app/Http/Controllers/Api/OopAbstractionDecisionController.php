<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanOopAbstractionDecisionRequest;
use App\Services\Practice\OopAbstractionDecisionService;
use Illuminate\Http\JsonResponse;

final class OopAbstractionDecisionController extends Controller
{
    /**
     * Return a PHP OOP abstraction decision plan.
     */
    public function __invoke(PlanOopAbstractionDecisionRequest $request, OopAbstractionDecisionService $planner): JsonResponse
    {
        return $this->jsonData($planner->decide($request->planData()));
    }
}
