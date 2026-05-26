<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyLearningPipelineService;
use Illuminate\Contracts\View\View;

final class TechnologyLearningPipelineController extends Controller
{
    /**
     * Show the complete learning pipeline for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyLearningPipelineService $pipelines
    ): View {
        $filters = $request->sourceFilters(defaultFamily: 'laravel', defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-learning-pipeline', [
            'pipeline' => $pipelines->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
