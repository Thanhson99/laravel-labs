<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeArchiveRetrievalLabService;
use Illuminate\Http\JsonResponse;

final class PracticeArchiveRetrievalLabController extends Controller
{
    /**
     * Return retrieval cards generated from archive entries.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeArchiveRetrievalLabService $retrievals): JsonResponse
    {
        return $this->jsonData($retrievals->build($request->pipelineFilters()));
    }
}
