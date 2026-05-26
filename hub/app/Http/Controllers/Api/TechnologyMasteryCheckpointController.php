<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyMasteryCheckpointService;
use Illuminate\Http\JsonResponse;

final class TechnologyMasteryCheckpointController extends Controller
{
    /**
     * Return a promote-or-repeat checkpoint for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyMasteryCheckpointService $checkpoints
    ): JsonResponse {
        return $this->jsonData($checkpoints->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
