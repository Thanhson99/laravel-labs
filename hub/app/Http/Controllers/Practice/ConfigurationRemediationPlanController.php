<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationRemediationPlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationRemediationPlanController extends Controller
{
    /**
     * Show remediation tasks for app, auth, and quality-gate configuration risks.
     */
    public function __invoke(ConfigurationRemediationPlanService $remediationPlan): View
    {
        return view('practice.configuration-remediation-plan', [
            'remediationPlan' => $remediationPlan->build(),
        ]);
    }
}
