<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationSpacedReviewService;
use Illuminate\Contracts\View\View;

final class ConfigurationSpacedReviewController extends Controller
{
    /**
     * Show day 1, day 3, and day 7 review cards for configuration practice.
     */
    public function __invoke(ConfigurationSpacedReviewService $spacedReview): View
    {
        return view('practice.configuration-spaced-review', [
            'spacedReview' => $spacedReview->build(),
        ]);
    }
}
