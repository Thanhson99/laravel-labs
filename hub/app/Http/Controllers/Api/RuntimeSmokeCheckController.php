<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\RuntimeSmokeCheckService;
use Illuminate\Http\JsonResponse;

final class RuntimeSmokeCheckController extends Controller
{
    /**
     * Return local runtime checks for Docker practice.
     */
    public function __invoke(RuntimeSmokeCheckService $runtime): JsonResponse
    {
        return $this->jsonData($runtime->report());
    }
}
