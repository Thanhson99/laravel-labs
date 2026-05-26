<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeKnowledgeGapLabService;
use Illuminate\Contracts\View\View;

final class PracticeKnowledgeGapLabController extends Controller
{
    /**
     * Show knowledge-gap cards generated from interview defenses.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeKnowledgeGapLabService $gaps): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.knowledge-gap-lab', [
            'filters' => $filters,
            'lab' => $gaps->build($filters),
        ]);
    }
}
