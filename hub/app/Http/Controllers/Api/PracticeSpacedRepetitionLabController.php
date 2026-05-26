<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSpacedRepetitionLabService;
use Illuminate\Http\JsonResponse;

final class PracticeSpacedRepetitionLabController extends Controller
{
    /**
     * Return spaced repetition sessions generated from knowledge gaps.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSpacedRepetitionLabService $reviews): JsonResponse
    {
        return $this->jsonData($reviews->build($request->pipelineFilters()));
    }
}
