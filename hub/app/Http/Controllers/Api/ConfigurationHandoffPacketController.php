<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationHandoffPacketService;
use Illuminate\Http\JsonResponse;

final class ConfigurationHandoffPacketController extends Controller
{
    /**
     * Return the final handoff packet for configuration practice.
     */
    public function __invoke(ConfigurationHandoffPacketService $packet): JsonResponse
    {
        return $this->jsonData($packet->build());
    }
}
