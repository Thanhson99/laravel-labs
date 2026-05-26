<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeReviewLabService;
use Illuminate\Http\JsonResponse;

final class PracticeReviewLabController extends Controller
{
    /**
     * Return a review checklist for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeReviewLabService $reviews): JsonResponse
    {
        $review = $reviews->build($request->contentFilters());

        return $this->jsonDataOrNotFound($review, 'Practice review lab not found.');
    }
}
