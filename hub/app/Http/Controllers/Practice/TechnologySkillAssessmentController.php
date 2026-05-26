<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologySkillAssessmentService;
use Illuminate\Contracts\View\View;

final class TechnologySkillAssessmentController extends Controller
{
    /**
     * Show a scored skill assessment for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologySkillAssessmentService $assessments
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-skill-assessment', [
            'assessment' => $assessments->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
