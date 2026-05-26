<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationMasteryCheckpointService;
use Illuminate\Http\JsonResponse;

final class ConfigurationMasteryCheckpointController extends Controller
{
    /**
     * Return the promote-or-repeat checkpoint for configuration practice.
     */
    public function __invoke(ConfigurationMasteryCheckpointService $checkpoint): JsonResponse
    {
        return $this->jsonData($checkpoint->build());
    }
}
