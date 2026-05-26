<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeDemoScriptLabService;
use Illuminate\Contracts\View\View;

final class PracticeDemoScriptLabController extends Controller
{
    /**
     * Show a demo script for a weekly practice report.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeDemoScriptLabService $scripts): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.demo-script-lab', [
            'filters' => $filters,
            'script' => $scripts->build($filters),
        ]);
    }
}
