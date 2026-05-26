<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeBugFixLabService;
use Illuminate\Http\JsonResponse;

final class PracticeBugFixLabController extends Controller
{
    /**
     * Return bug-fix drills generated from live coding rounds.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeBugFixLabService $drills): JsonResponse
    {
        return $this->jsonData($drills->build($request->pipelineFilters()));
    }
}
