<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeChallengeExecutionLabService;
use Illuminate\Http\JsonResponse;

final class PracticeChallengeExecutionLabController extends Controller
{
    /**
     * Return executable challenge steps generated from next challenges.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeChallengeExecutionLabService $executions): JsonResponse
    {
        return $this->jsonData($executions->build($request->pipelineFilters()));
    }
}
