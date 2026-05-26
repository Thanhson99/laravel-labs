<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyImplementationLabService;
use Illuminate\Http\JsonResponse;

final class TechnologyImplementationLabController extends Controller
{
    /**
     * Return a sequential implementation lab for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyImplementationLabService $labs
    ): JsonResponse {
        return $this->jsonData($labs->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
