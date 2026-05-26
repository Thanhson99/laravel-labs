<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticePortfolioLabService;
use Illuminate\Http\JsonResponse;

final class PracticePortfolioLabController extends Controller
{
    /**
     * Return a portfolio entry for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticePortfolioLabService $portfolios): JsonResponse
    {
        $portfolio = $portfolios->build($request->contentFilters());

        return $this->jsonDataOrNotFound($portfolio, 'Practice portfolio lab not found.');
    }
}
