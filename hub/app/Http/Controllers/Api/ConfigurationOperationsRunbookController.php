<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationOperationsRunbookService;
use Illuminate\Http\JsonResponse;

final class ConfigurationOperationsRunbookController extends Controller
{
    /**
     * Return the operations runbook for configuration practice.
     */
    public function __invoke(ConfigurationOperationsRunbookService $runbook): JsonResponse
    {
        return $this->jsonData($runbook->build());
    }
}
