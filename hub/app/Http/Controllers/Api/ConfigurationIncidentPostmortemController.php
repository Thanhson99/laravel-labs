<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationIncidentPostmortemService;
use Illuminate\Http\JsonResponse;

final class ConfigurationIncidentPostmortemController extends Controller
{
    /**
     * Return the postmortem for configuration incident practice.
     */
    public function __invoke(ConfigurationIncidentPostmortemService $postmortem): JsonResponse
    {
        return $this->jsonData($postmortem->build());
    }
}
