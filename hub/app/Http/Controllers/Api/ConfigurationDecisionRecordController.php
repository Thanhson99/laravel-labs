<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationDecisionRecordService;
use Illuminate\Http\JsonResponse;

final class ConfigurationDecisionRecordController extends Controller
{
    /**
     * Return the ADR-style decision record for configuration practice.
     */
    public function __invoke(ConfigurationDecisionRecordService $decisionRecord): JsonResponse
    {
        return $this->jsonData($decisionRecord->build());
    }
}
