<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeInterviewDefenseLabService;
use Illuminate\Contracts\View\View;

final class PracticeInterviewDefenseLabController extends Controller
{
    /**
     * Show interview defense cards generated from release evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeInterviewDefenseLabService $defenses): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.interview-defense-lab', [
            'filters' => $filters,
            'lab' => $defenses->build($filters),
        ]);
    }
}
