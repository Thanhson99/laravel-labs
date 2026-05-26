<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationRemediationPlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationRemediationPlanController extends Controller
{
    /**
     * Return remediation tasks for app, auth, and quality-gate configuration risks.
     */
    public function __invoke(ConfigurationRemediationPlanService $remediationPlan): JsonResponse
    {
        return $this->jsonData($remediationPlan->build());
    }
}
