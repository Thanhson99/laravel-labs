<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationReadinessService;
use Illuminate\Http\JsonResponse;

final class ConfigurationReadinessController extends Controller
{
    /**
     * Return a read-only readiness report for app and auth configuration.
     */
    public function __invoke(ConfigurationReadinessService $readiness): JsonResponse
    {
        return $this->jsonData($readiness->build());
    }
}
