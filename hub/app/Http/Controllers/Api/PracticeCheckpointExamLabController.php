<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeCheckpointExamLabService;
use Illuminate\Http\JsonResponse;

final class PracticeCheckpointExamLabController extends Controller
{
    /**
     * Return a timed checkpoint exam for one technology.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeCheckpointExamLabService $exams): JsonResponse
    {
        return $this->jsonData($exams->build($request->requiredTechnologyFilters(defaultLimit: 2)));
    }
}
