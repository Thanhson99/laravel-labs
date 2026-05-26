<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeKnowledgeGapLabService;
use Illuminate\Http\JsonResponse;

final class PracticeKnowledgeGapLabController extends Controller
{
    /**
     * Return knowledge-gap cards generated from interview defenses.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeKnowledgeGapLabService $gaps): JsonResponse
    {
        return $this->jsonData($gaps->build($request->pipelineFilters()));
    }
}
