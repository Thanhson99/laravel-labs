<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeEvidenceReusePlanLabService;
use Illuminate\Http\JsonResponse;

final class PracticeEvidenceReusePlanLabController extends Controller
{
    /**
     * Return reuse plans generated from archive retrieval cards.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeEvidenceReusePlanLabService $plans): JsonResponse
    {
        return $this->jsonData($plans->build($request->pipelineFilters()));
    }
}
