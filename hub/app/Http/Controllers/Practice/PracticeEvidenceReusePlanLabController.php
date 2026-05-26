<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeEvidenceReusePlanLabService;
use Illuminate\Contracts\View\View;

final class PracticeEvidenceReusePlanLabController extends Controller
{
    /**
     * Show reuse plans generated from archive retrieval cards.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeEvidenceReusePlanLabService $plans): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.evidence-reuse-plan-lab', [
            'filters' => $filters,
            'lab' => $plans->build($filters),
        ]);
    }
}
