<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeLiveCodingLabService;
use Illuminate\Http\JsonResponse;

final class PracticeLiveCodingLabController extends Controller
{
    /**
     * Return a live coding plan generated from a demo script.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeLiveCodingLabService $sessions): JsonResponse
    {
        return $this->jsonData($sessions->build($request->pipelineFilters()));
    }
}
