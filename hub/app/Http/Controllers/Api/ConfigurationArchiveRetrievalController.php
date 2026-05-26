<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationArchiveRetrievalService;
use Illuminate\Http\JsonResponse;

final class ConfigurationArchiveRetrievalController extends Controller
{
    /**
     * Return retrieval drills for archived configuration evidence.
     */
    public function __invoke(ConfigurationArchiveRetrievalService $retrieval): JsonResponse
    {
        return $this->jsonData($retrieval->build());
    }
}
