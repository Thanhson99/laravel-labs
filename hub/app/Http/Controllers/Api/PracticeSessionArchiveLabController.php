<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSessionArchiveLabService;
use Illuminate\Http\JsonResponse;

final class PracticeSessionArchiveLabController extends Controller
{
    /**
     * Return archive entries generated from session debrief cards.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSessionArchiveLabService $archives): JsonResponse
    {
        return $this->jsonData($archives->build($request->pipelineFilters()));
    }
}
