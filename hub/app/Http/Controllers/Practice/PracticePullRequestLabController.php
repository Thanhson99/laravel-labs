<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticePullRequestLabService;
use Illuminate\Contracts\View\View;

final class PracticePullRequestLabController extends Controller
{
    /**
     * Show pull-request artifacts for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticePullRequestLabService $pullRequests): View
    {
        $filters = $request->contentFilters();

        return view('practice.pull-request-lab', [
            'filters' => $filters,
            'pullRequest' => $pullRequests->build($filters),
        ]);
    }
}
