<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationAssessmentService;
use Illuminate\Contracts\View\View;

final class ConfigurationAssessmentController extends Controller
{
    /**
     * Show the scored assessment for configuration remediation PR work.
     */
    public function __invoke(ConfigurationAssessmentService $assessment): View
    {
        return view('practice.configuration-assessment', [
            'assessment' => $assessment->build(),
        ]);
    }
}
