<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationChangeChecklistService;
use Illuminate\Http\JsonResponse;

final class ConfigurationChangeChecklistController extends Controller
{
    /**
     * Return a review checklist for app, auth, and quality-gate configuration changes.
     */
    public function __invoke(ConfigurationChangeChecklistService $checklist): JsonResponse
    {
        return $this->jsonData($checklist->build());
    }
}
