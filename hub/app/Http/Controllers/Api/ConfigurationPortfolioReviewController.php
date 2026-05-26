<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPortfolioReviewService;
use Illuminate\Http\JsonResponse;

final class ConfigurationPortfolioReviewController extends Controller
{
    /**
     * Return the scored review for the configuration portfolio brief.
     */
    public function __invoke(ConfigurationPortfolioReviewService $review): JsonResponse
    {
        return $this->jsonData($review->build());
    }
}
