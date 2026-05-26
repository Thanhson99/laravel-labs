<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPortfolioBriefService;
use Illuminate\Http\JsonResponse;

final class ConfigurationPortfolioBriefController extends Controller
{
    /**
     * Return the portfolio brief for configuration evidence.
     */
    public function __invoke(ConfigurationPortfolioBriefService $brief): JsonResponse
    {
        return $this->jsonData($brief->build());
    }
}
