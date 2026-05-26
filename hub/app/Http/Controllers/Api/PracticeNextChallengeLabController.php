<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeNextChallengeLabService;
use Illuminate\Http\JsonResponse;

final class PracticeNextChallengeLabController extends Controller
{
    /**
     * Return next challenge cards generated from competency maps.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeNextChallengeLabService $challenges): JsonResponse
    {
        return $this->jsonData($challenges->build($request->pipelineFilters()));
    }
}
