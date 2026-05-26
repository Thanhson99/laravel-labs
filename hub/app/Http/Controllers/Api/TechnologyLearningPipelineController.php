<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyLearningPipelineService;
use Illuminate\Http\JsonResponse;

final class TechnologyLearningPipelineController extends Controller
{
    /**
     * Return the complete learning pipeline for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyLearningPipelineService $pipelines
    ): JsonResponse {
        return $this->jsonData($pipelines->build(
            $technology,
            $request->sourceFilters(defaultFamily: 'laravel', defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
