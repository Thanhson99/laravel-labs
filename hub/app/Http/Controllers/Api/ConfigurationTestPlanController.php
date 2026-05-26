<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationTestPlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationTestPlanController extends Controller
{
    /**
     * Return a PHPUnit test plan for app and auth configuration contracts.
     */
    public function __invoke(ConfigurationTestPlanService $testPlan): JsonResponse
    {
        return $this->jsonData($testPlan->build());
    }
}
