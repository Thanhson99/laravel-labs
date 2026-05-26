<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologySkillAssessmentService;
use Illuminate\Http\JsonResponse;

final class TechnologySkillAssessmentController extends Controller
{
    /**
     * Return a scored skill assessment for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologySkillAssessmentService $assessments
    ): JsonResponse {
        return $this->jsonData($assessments->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
