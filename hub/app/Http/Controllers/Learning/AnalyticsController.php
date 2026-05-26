<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Services\Learning\LearningAnalyticsService;
use Illuminate\Contracts\View\View;

final class AnalyticsController extends Controller
{
    /**
     * Show analytics for the integrated JSON learning content.
     */
    public function __invoke(LearningAnalyticsService $analytics): View
    {
        return view('learning.analytics', [
            'report' => $analytics->report(),
        ]);
    }
}
