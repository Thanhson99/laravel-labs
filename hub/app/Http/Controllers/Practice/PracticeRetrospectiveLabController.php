<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeRetrospectiveLabService;
use Illuminate\Contracts\View\View;

final class PracticeRetrospectiveLabController extends Controller
{
    /**
     * Show retrospective prompts for one assessed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeRetrospectiveLabService $retrospectives): View
    {
        $filters = $request->contentFilters();

        return view('practice.retrospective-lab', [
            'filters' => $filters,
            'retrospective' => $retrospectives->build($filters),
        ]);
    }
}
