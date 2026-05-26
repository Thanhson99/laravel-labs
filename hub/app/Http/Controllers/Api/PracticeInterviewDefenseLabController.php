<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeInterviewDefenseLabService;
use Illuminate\Http\JsonResponse;

final class PracticeInterviewDefenseLabController extends Controller
{
    /**
     * Return interview defense cards generated from release evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeInterviewDefenseLabService $defenses): JsonResponse
    {
        return $this->jsonData($defenses->build($request->pipelineFilters()));
    }
}
