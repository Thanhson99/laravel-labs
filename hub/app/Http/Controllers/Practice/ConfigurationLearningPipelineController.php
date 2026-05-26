<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationLearningPipelineService;
use Illuminate\Contracts\View\View;

final class ConfigurationLearningPipelineController extends Controller
{
    /**
     * Show the complete app and auth configuration learning pipeline.
     */
    public function __invoke(ConfigurationLearningPipelineService $pipeline): View
    {
        return view('practice.configuration-learning-pipeline', [
            'pipeline' => $pipeline->build(),
        ]);
    }
}
