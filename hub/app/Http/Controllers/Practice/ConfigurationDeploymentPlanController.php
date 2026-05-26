<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationDeploymentPlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationDeploymentPlanController extends Controller
{
    /**
     * Show deployment guidance for app, auth, and quality-gate configuration changes.
     */
    public function __invoke(ConfigurationDeploymentPlanService $deploymentPlan): View
    {
        return view('practice.configuration-deployment-plan', [
            'deploymentPlan' => $deploymentPlan->build(),
        ]);
    }
}
