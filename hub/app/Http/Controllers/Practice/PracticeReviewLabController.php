<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeReviewLabService;
use Illuminate\Contracts\View\View;

final class PracticeReviewLabController extends Controller
{
    /**
     * Show a review checklist for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeReviewLabService $reviews): View
    {
        $filters = $request->contentFilters();

        return view('practice.review-lab', [
            'filters' => $filters,
            'review' => $reviews->build($filters),
        ]);
    }
}
