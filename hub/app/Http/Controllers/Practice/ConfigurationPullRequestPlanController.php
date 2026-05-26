<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPullRequestPlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationPullRequestPlanController extends Controller
{
    /**
     * Show pull request artifacts for configuration remediation work.
     */
    public function __invoke(ConfigurationPullRequestPlanService $pullRequestPlan): View
    {
        return view('practice.configuration-pull-request-plan', [
            'pullRequestPlan' => $pullRequestPlan->build(),
        ]);
    }
}
