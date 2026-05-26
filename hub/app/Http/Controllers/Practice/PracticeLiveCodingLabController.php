<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeLiveCodingLabService;
use Illuminate\Contracts\View\View;

final class PracticeLiveCodingLabController extends Controller
{
    /**
     * Show a live coding plan generated from a demo script.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeLiveCodingLabService $sessions): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.live-coding-lab', [
            'filters' => $filters,
            'session' => $sessions->build($filters),
        ]);
    }
}
