<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationNextSessionPlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationNextSessionPlanController extends Controller
{
    /**
     * Show the next-session plan for configuration practice.
     */
    public function __invoke(ConfigurationNextSessionPlanService $plan): View
    {
        return view('practice.configuration-next-session-plan', [
            'plan' => $plan->build(),
        ]);
    }
}
