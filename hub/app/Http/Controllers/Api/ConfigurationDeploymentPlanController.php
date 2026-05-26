<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationDeploymentPlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationDeploymentPlanController extends Controller
{
    /**
     * Return deployment guidance for app, auth, and quality-gate configuration changes.
     */
    public function __invoke(ConfigurationDeploymentPlanService $deploymentPlan): JsonResponse
    {
        return $this->jsonData($deploymentPlan->build());
    }
}
