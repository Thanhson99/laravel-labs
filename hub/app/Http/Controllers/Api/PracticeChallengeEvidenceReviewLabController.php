<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeChallengeEvidenceReviewLabService;
use Illuminate\Http\JsonResponse;

final class PracticeChallengeEvidenceReviewLabController extends Controller
{
    /**
     * Return review cards for submitted challenge evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeChallengeEvidenceReviewLabService $reviews): JsonResponse
    {
        return $this->jsonData($reviews->build($request->pipelineFilters()));
    }
}
