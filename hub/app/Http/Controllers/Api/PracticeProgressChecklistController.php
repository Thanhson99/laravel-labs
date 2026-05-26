<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SummarizePracticeProgressRequest;
use App\Services\Practice\PracticeProgressChecklistService;
use Illuminate\Http\JsonResponse;

final class PracticeProgressChecklistController extends Controller
{
    /**
     * Summarize a practice checklist payload.
     */
    public function store(
        SummarizePracticeProgressRequest $request,
        PracticeProgressChecklistService $progress,
    ): JsonResponse {
        return $this->jsonData($progress->summarize($request->items()));
    }
}
