<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticePortfolioLabService;
use Illuminate\Contracts\View\View;

final class PracticePortfolioLabController extends Controller
{
    /**
     * Show a portfolio entry for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticePortfolioLabService $portfolios): View
    {
        $filters = $request->contentFilters();

        return view('practice.portfolio-lab', [
            'filters' => $filters,
            'portfolio' => $portfolios->build($filters),
        ]);
    }
}
