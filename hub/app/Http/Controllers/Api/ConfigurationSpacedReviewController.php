<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationSpacedReviewService;
use Illuminate\Http\JsonResponse;

final class ConfigurationSpacedReviewController extends Controller
{
    /**
     * Return day 1, day 3, and day 7 review cards for configuration practice.
     */
    public function __invoke(ConfigurationSpacedReviewService $spacedReview): JsonResponse
    {
        return $this->jsonData($spacedReview->build());
    }
}
