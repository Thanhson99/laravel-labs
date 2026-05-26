<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeReleaseReadinessLabService;
use Illuminate\Contracts\View\View;

final class PracticeReleaseReadinessLabController extends Controller
{
    /**
     * Show release readiness artifacts generated from refactor tasks.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeReleaseReadinessLabService $releases): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.release-readiness-lab', [
            'filters' => $filters,
            'lab' => $releases->build($filters),
        ]);
    }
}
