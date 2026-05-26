<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPortfolioReviewService;
use Illuminate\Contracts\View\View;

final class ConfigurationPortfolioReviewController extends Controller
{
    /**
     * Show the scored review for the configuration portfolio brief.
     */
    public function __invoke(ConfigurationPortfolioReviewService $review): View
    {
        return view('practice.configuration-portfolio-review', [
            'review' => $review->build(),
        ]);
    }
}
