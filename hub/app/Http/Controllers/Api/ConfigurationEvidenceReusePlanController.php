<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationEvidenceReusePlanService;
use Illuminate\Http\JsonResponse;

final class ConfigurationEvidenceReusePlanController extends Controller
{
    /**
     * Return reuse tasks for retrieved configuration evidence.
     */
    public function __invoke(ConfigurationEvidenceReusePlanService $reusePlan): JsonResponse
    {
        return $this->jsonData($reusePlan->build());
    }
}
