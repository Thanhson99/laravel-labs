<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeMasteryEvidenceLabService;
use Illuminate\Contracts\View\View;

final class PracticeMasteryEvidenceLabController extends Controller
{
    /**
     * Show mastery evidence cards generated from repeated practice.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeMasteryEvidenceLabService $evidence): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.mastery-evidence-lab', [
            'filters' => $filters,
            'lab' => $evidence->build($filters),
        ]);
    }
}
