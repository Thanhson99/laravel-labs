<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyPortfolioArtifactService;
use Illuminate\Contracts\View\View;

final class TechnologyPortfolioArtifactController extends Controller
{
    /**
     * Show a portfolio-ready artifact for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyPortfolioArtifactService $artifacts
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-portfolio-artifact', [
            'artifact' => $artifacts->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
