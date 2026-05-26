<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeMasteryEvidenceLabService;
use Illuminate\Http\JsonResponse;

final class PracticeMasteryEvidenceLabController extends Controller
{
    /**
     * Return mastery evidence cards generated from repeated practice.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeMasteryEvidenceLabService $evidence): JsonResponse
    {
        return $this->jsonData($evidence->build($request->pipelineFilters()));
    }
}
