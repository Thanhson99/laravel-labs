<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSessionReplayLabService;
use Illuminate\Http\JsonResponse;

final class PracticeSessionReplayLabController extends Controller
{
    /**
     * Return replay rounds generated from next-session handoffs.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSessionReplayLabService $replays): JsonResponse
    {
        return $this->jsonData($replays->build($request->pipelineFilters()));
    }
}
