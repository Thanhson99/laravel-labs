<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationReleaseEvidenceService;
use Illuminate\Http\JsonResponse;

final class ConfigurationReleaseEvidenceController extends Controller
{
    /**
     * Return release evidence for app, auth, and quality-gate configuration work.
     */
    public function __invoke(ConfigurationReleaseEvidenceService $releaseEvidence): JsonResponse
    {
        return $this->jsonData($releaseEvidence->build());
    }
}
