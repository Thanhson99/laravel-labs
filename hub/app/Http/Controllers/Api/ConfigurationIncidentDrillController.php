<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationIncidentDrillService;
use Illuminate\Http\JsonResponse;

final class ConfigurationIncidentDrillController extends Controller
{
    /**
     * Return the incident drill for configuration practice.
     */
    public function __invoke(ConfigurationIncidentDrillService $incidentDrill): JsonResponse
    {
        return $this->jsonData($incidentDrill->build());
    }
}
