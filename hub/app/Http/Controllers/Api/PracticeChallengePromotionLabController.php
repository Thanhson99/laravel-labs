<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeChallengePromotionLabService;
use Illuminate\Http\JsonResponse;

final class PracticeChallengePromotionLabController extends Controller
{
    /**
     * Return promotion decisions generated from reviewed challenge evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeChallengePromotionLabService $promotions): JsonResponse
    {
        return $this->jsonData($promotions->build($request->pipelineFilters()));
    }
}
