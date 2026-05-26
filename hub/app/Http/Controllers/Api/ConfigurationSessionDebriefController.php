<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationSessionDebriefService;
use Illuminate\Http\JsonResponse;

final class ConfigurationSessionDebriefController extends Controller
{
    /**
     * Return the debrief for a configuration follow-up session.
     */
    public function __invoke(ConfigurationSessionDebriefService $debrief): JsonResponse
    {
        return $this->jsonData($debrief->build());
    }
}
