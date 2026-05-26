<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeRemediationLabService;
use Illuminate\Contracts\View\View;

final class PracticeRemediationLabController extends Controller
{
    /**
     * Show remediation tasks for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeRemediationLabService $remediation): View
    {
        $filters = $request->contentFilters();

        return view('practice.remediation-lab', [
            'filters' => $filters,
            'remediation' => $remediation->build($filters),
        ]);
    }
}
