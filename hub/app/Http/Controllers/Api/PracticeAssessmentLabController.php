<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeAssessmentLabService;
use Illuminate\Http\JsonResponse;

final class PracticeAssessmentLabController extends Controller
{
    /**
     * Return a self-assessment rubric for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeAssessmentLabService $assessments): JsonResponse
    {
        $assessment = $assessments->build($request->contentFilters());

        return $this->jsonDataOrNotFound($assessment, 'Practice assessment lab not found.');
    }
}
