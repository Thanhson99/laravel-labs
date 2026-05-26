<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationEvidenceArchiveService;
use Illuminate\Http\JsonResponse;

final class ConfigurationEvidenceArchiveController extends Controller
{
    /**
     * Return archived evidence for configuration practice.
     */
    public function __invoke(ConfigurationEvidenceArchiveService $archive): JsonResponse
    {
        return $this->jsonData($archive->build());
    }
}
