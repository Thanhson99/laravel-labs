<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeArchiveRetrievalLabService;
use Illuminate\Contracts\View\View;

final class PracticeArchiveRetrievalLabController extends Controller
{
    /**
     * Show retrieval cards generated from archive entries.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeArchiveRetrievalLabService $retrievals): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.archive-retrieval-lab', [
            'filters' => $filters,
            'lab' => $retrievals->build($filters),
        ]);
    }
}
