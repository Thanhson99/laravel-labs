<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPracticeDashboardService;
use Illuminate\Http\JsonResponse;

final class ConfigurationPracticeDashboardController extends Controller
{
    /**
     * Return a compact dashboard for the app and auth configuration practice pipeline.
     */
    public function __invoke(ConfigurationPracticeDashboardService $dashboard): JsonResponse
    {
        return $this->jsonData($dashboard->build());
    }
}
