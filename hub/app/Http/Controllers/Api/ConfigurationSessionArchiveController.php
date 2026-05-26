<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationSessionArchiveService;
use Illuminate\Http\JsonResponse;

final class ConfigurationSessionArchiveController extends Controller
{
    /**
     * Return the archive entry for a configuration follow-up session.
     */
    public function __invoke(ConfigurationSessionArchiveService $archive): JsonResponse
    {
        return $this->jsonData($archive->build());
    }
}
