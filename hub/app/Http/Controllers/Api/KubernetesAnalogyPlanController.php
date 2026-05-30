<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanKubernetesAnalogyRequest;
use App\Services\Practice\KubernetesAnalogyPlanService;
use Illuminate\Http\JsonResponse;

final class KubernetesAnalogyPlanController extends Controller
{
    /**
     * Return a Kubernetes ship-analogy plan for DevOps beginners.
     */
    public function __invoke(PlanKubernetesAnalogyRequest $request, KubernetesAnalogyPlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
