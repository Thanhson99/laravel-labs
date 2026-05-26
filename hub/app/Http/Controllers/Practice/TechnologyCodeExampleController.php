<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyCodeExampleService;
use Illuminate\Contracts\View\View;

final class TechnologyCodeExampleController extends Controller
{
    /**
     * Show technology-specific code examples generated from JSON content.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyCodeExampleService $examples): View
    {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 50);

        return view('practice.technology-code-examples', [
            'examples' => $examples->build($filters),
            'filters' => $filters,
        ]);
    }
}
