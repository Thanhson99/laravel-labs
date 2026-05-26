<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationAssessmentService;
use Illuminate\Http\JsonResponse;

final class ConfigurationAssessmentController extends Controller
{
    /**
     * Return the scored assessment for configuration remediation PR work.
     */
    public function __invoke(ConfigurationAssessmentService $assessment): JsonResponse
    {
        return $this->jsonData($assessment->build());
    }
}
