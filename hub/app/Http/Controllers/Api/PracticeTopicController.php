<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePracticeTopicRequest;
use App\Services\Practice\PracticeTopicIntakeService;
use Illuminate\Http\JsonResponse;

final class PracticeTopicController extends Controller
{
    /**
     * Accept a validated practice topic payload.
     */
    public function store(StorePracticeTopicRequest $request, PracticeTopicIntakeService $topics): JsonResponse
    {
        return $this->jsonDataWithMessage(
            $topics->create($request->topicData()),
            'Practice topic accepted.',
            201,
        );
    }
}
