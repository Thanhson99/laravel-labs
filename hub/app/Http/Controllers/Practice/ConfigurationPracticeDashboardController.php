<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPracticeDashboardService;
use Illuminate\Contracts\View\View;

final class ConfigurationPracticeDashboardController extends Controller
{
    /**
     * Show a compact dashboard for the app and auth configuration practice pipeline.
     */
    public function __invoke(ConfigurationPracticeDashboardService $dashboard): View
    {
        return view('practice.configuration-dashboard', [
            'dashboard' => $dashboard->build(),
        ]);
    }
}
