<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationArchiveRefreshPlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationArchiveRefreshPlanController extends Controller
{
    /**
     * Return the refresh plan for archived configuration evidence.
     */
    public function __invoke(ConfigurationArchiveRefreshPlanService $refreshPlan): JsonResponse
    {
        return $this->jsonData($refreshPlan->build());
    }
}
