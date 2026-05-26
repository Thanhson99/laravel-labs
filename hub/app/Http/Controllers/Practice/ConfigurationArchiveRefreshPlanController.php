<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationArchiveRefreshPlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationArchiveRefreshPlanController extends Controller
{
    /**
     * Show the refresh plan for archived configuration evidence.
     */
    public function __invoke(ConfigurationArchiveRefreshPlanService $refreshPlan): View
    {
        return view('practice.configuration-archive-refresh-plan', [
            'refreshPlan' => $refreshPlan->build(),
        ]);
    }
}
