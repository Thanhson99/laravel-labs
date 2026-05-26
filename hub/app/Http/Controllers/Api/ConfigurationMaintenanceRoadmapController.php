<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationMaintenanceRoadmapService;
use Illuminate\Http\JsonResponse;

final class ConfigurationMaintenanceRoadmapController extends Controller
{
    /**
     * Return the maintenance roadmap for configuration evidence.
     */
    public function __invoke(ConfigurationMaintenanceRoadmapService $roadmap): JsonResponse
    {
        return $this->jsonData($roadmap->build());
    }
}
