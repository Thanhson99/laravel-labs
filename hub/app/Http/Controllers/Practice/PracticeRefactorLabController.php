<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeRefactorLabService;
use Illuminate\Contracts\View\View;

final class PracticeRefactorLabController extends Controller
{
    /**
     * Show refactor tasks generated from bug-fix drills.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeRefactorLabService $refactors): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.refactor-lab', [
            'filters' => $filters,
            'lab' => $refactors->build($filters),
        ]);
    }
}
