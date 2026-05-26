<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeWeeklyReportLabService;
use Illuminate\Contracts\View\View;

final class PracticeWeeklyReportLabController extends Controller
{
    /**
     * Show a weekly practice report.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeWeeklyReportLabService $reports): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.weekly-report-lab', [
            'filters' => $filters,
            'report' => $reports->build($filters),
        ]);
    }
}
