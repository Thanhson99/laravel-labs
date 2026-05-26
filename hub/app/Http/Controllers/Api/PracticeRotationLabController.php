<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeRotationLabService;
use Illuminate\Http\JsonResponse;

final class PracticeRotationLabController extends Controller
{
    /**
     * Return a day-by-day practice rotation.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeRotationLabService $rotations): JsonResponse
    {
        return $this->jsonData($rotations->build($request->pipelineFilters()));
    }
}
