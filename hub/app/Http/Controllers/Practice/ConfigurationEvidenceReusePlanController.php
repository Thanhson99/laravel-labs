<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationEvidenceReusePlanService;
use Illuminate\Contracts\View\View;

final class ConfigurationEvidenceReusePlanController extends Controller
{
    /**
     * Show reuse tasks for retrieved configuration evidence.
     */
    public function __invoke(ConfigurationEvidenceReusePlanService $reusePlan): View
    {
        return view('practice.configuration-evidence-reuse-plan', [
            'reusePlan' => $reusePlan->build(),
        ]);
    }
}
