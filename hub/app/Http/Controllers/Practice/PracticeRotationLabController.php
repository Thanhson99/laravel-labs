<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeRotationLabService;
use Illuminate\Contracts\View\View;

final class PracticeRotationLabController extends Controller
{
    /**
     * Show a day-by-day practice rotation.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeRotationLabService $rotations): View
    {
        $filters = $request->pipelineFilters();

        return view('practice.rotation-lab', [
            'filters' => $filters,
            'rotation' => $rotations->build($filters),
        ]);
    }
}
