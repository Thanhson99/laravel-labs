<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationTestPlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationTestPlanController extends Controller
{
    /**
     * Show a PHPUnit test plan for app and auth configuration contracts.
     */
    public function __invoke(ConfigurationTestPlanService $testPlan): View
    {
        return view('practice.configuration-test-plan', [
            'testPlan' => $testPlan->build(),
        ]);
    }
}
