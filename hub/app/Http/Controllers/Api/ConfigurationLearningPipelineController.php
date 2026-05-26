<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationLearningPipelineService;
use Illuminate\Http\JsonResponse;

final class ConfigurationLearningPipelineController extends Controller
{
    /**
     * Return the complete app and auth configuration learning pipeline.
     */
    public function __invoke(ConfigurationLearningPipelineService $pipeline): JsonResponse
    {
        return $this->jsonData($pipeline->build());
    }
}
