<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyPracticeMatrixService;
use Illuminate\Contracts\View\View;

final class TechnologyPracticeMatrixController extends Controller
{
    /**
     * Show content coverage by technology with native practice links.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyPracticeMatrixService $matrix): View
    {
        $filters = $request->sourceFilters(defaultLanguage: 'en');

        return view('practice.technology-matrix', [
            'filters' => $filters,
            'matrix' => $matrix->build($filters),
        ]);
    }
}
