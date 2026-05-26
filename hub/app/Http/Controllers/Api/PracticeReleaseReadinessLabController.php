<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeReleaseReadinessLabService;
use Illuminate\Http\JsonResponse;

final class PracticeReleaseReadinessLabController extends Controller
{
    /**
     * Return release readiness artifacts generated from refactor tasks.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeReleaseReadinessLabService $releases): JsonResponse
    {
        return $this->jsonData($releases->build($request->pipelineFilters()));
    }
}
