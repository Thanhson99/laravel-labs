<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeAssessmentLabService;
use Illuminate\Contracts\View\View;

final class PracticeAssessmentLabController extends Controller
{
    /**
     * Show a self-assessment rubric for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeAssessmentLabService $assessments): View
    {
        $filters = $request->contentFilters();

        return view('practice.assessment-lab', [
            'assessment' => $assessments->build($filters),
            'filters' => $filters,
        ]);
    }
}
