<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologySpacedReviewService;
use Illuminate\Contracts\View\View;

final class TechnologySpacedReviewController extends Controller
{
    /**
     * Show spaced review cards for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologySpacedReviewService $reviews
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-spaced-review', [
            'review' => $reviews->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
