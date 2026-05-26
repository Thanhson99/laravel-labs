<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPullRequestPlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationPullRequestPlanController extends Controller
{
    /**
     * Return pull request artifacts for configuration remediation work.
     */
    public function __invoke(ConfigurationPullRequestPlanService $pullRequestPlan): JsonResponse
    {
        return $this->jsonData($pullRequestPlan->build());
    }
}
