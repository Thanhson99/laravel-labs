<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyCodeExampleService;
use Illuminate\Contracts\View\View;

final class TechnologyCodeExampleDetailController extends Controller
{
    /**
     * Show record-level code examples for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyCodeExampleService $examples
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 12);

        return view('practice.technology-code-example-detail', [
            'examples' => $examples->buildForTechnology($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
