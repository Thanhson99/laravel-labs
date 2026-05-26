<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyPipelineIndexService;
use Illuminate\Contracts\View\View;

final class TechnologyPipelineIndexController extends Controller
{
    /**
     * Show the discoverable index of technology learning pipelines.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyPipelineIndexService $pipelines): View
    {
        $filters = $request->sourceFilters(defaultLanguage: 'en');

        return view('practice.technology-pipeline-index', [
            'filters' => $filters,
            'pipelines' => $pipelines->build($filters),
        ]);
    }
}
