<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSessionDebriefLabService;
use Illuminate\Http\JsonResponse;

final class PracticeSessionDebriefLabController extends Controller
{
    /**
     * Return debrief cards generated from session replay rounds.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSessionDebriefLabService $debriefs): JsonResponse
    {
        return $this->jsonData($debriefs->build($request->pipelineFilters()));
    }
}
