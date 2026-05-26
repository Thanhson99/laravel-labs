<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeMentorFeedbackLabService;
use Illuminate\Http\JsonResponse;

final class PracticeMentorFeedbackLabController extends Controller
{
    /**
     * Return mentor-style feedback for a technology capstone.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeMentorFeedbackLabService $feedback): JsonResponse
    {
        return $this->jsonData($feedback->build($request->requiredTechnologyFilters(defaultLimit: 3)));
    }
}
