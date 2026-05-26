<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationRiskRegisterService;
use Illuminate\Http\JsonResponse;

final class ConfigurationRiskRegisterController extends Controller
{
    /**
     * Return the risk register for app, auth, and quality-gate configuration practice.
     */
    public function __invoke(ConfigurationRiskRegisterService $riskRegister): JsonResponse
    {
        return $this->jsonData($riskRegister->build());
    }
}
