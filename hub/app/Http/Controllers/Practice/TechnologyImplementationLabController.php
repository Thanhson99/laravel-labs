<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyImplementationLabService;
use Illuminate\Contracts\View\View;

final class TechnologyImplementationLabController extends Controller
{
    /**
     * Show a sequential implementation lab for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyImplementationLabService $labs
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-implementation-lab', [
            'lab' => $labs->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
