<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyPortfolioArtifactService;
use Illuminate\Http\JsonResponse;

final class TechnologyPortfolioArtifactController extends Controller
{
    /**
     * Return a portfolio-ready artifact for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyPortfolioArtifactService $artifacts
    ): JsonResponse {
        return $this->jsonData($artifacts->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
