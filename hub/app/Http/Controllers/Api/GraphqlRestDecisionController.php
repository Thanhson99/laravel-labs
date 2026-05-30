<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanGraphqlRestDecisionRequest;
use App\Services\Practice\GraphqlRestDecisionService;
use Illuminate\Http\JsonResponse;

final class GraphqlRestDecisionController extends Controller
{
    /**
     * Return a REST versus GraphQL API contract decision plan.
     */
    public function __invoke(PlanGraphqlRestDecisionRequest $request, GraphqlRestDecisionService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
