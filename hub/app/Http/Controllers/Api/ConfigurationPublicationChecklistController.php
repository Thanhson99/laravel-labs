<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPublicationChecklistService;
use Illuminate\Http\JsonResponse;

final class ConfigurationPublicationChecklistController extends Controller
{
    /**
     * Return the publication checklist for configuration evidence.
     */
    public function __invoke(ConfigurationPublicationChecklistService $checklist): JsonResponse
    {
        return $this->jsonData($checklist->build());
    }
}
