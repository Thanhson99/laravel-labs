<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSpacedRepetitionLabService;
use Illuminate\Contracts\View\View;

final class PracticeSpacedRepetitionLabController extends Controller
{
    /**
     * Show spaced repetition sessions generated from knowledge gaps.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSpacedRepetitionLabService $reviews): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.spaced-repetition-lab', [
            'filters' => $filters,
            'lab' => $reviews->build($filters),
        ]);
    }
}
