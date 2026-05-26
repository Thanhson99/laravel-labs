<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSessionArchiveLabService;
use Illuminate\Contracts\View\View;

final class PracticeSessionArchiveLabController extends Controller
{
    /**
     * Show archive entries generated from session debrief cards.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSessionArchiveLabService $archives): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.session-archive-lab', [
            'filters' => $filters,
            'lab' => $archives->build($filters),
        ]);
    }
}
