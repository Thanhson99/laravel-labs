<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologySpacedReviewService;
use Illuminate\Http\JsonResponse;

final class TechnologySpacedReviewController extends Controller
{
    /**
     * Return spaced review cards for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologySpacedReviewService $reviews
    ): JsonResponse {
        return $this->jsonData($reviews->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
