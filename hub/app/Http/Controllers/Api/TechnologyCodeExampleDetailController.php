<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyCodeExampleService;
use Illuminate\Http\JsonResponse;

final class TechnologyCodeExampleDetailController extends Controller
{
    /**
     * Return record-level code examples for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyCodeExampleService $examples
    ): JsonResponse {
        return $this->jsonData($examples->buildForTechnology(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 12),
        ));
    }
}
