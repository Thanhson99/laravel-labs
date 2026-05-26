<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationNextSessionPlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationNextSessionPlanController extends Controller
{
    /**
     * Return the next-session plan for configuration practice.
     */
    public function __invoke(ConfigurationNextSessionPlanService $plan): JsonResponse
    {
        return $this->jsonData($plan->build());
    }
}
