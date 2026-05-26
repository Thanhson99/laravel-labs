<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationReleaseEvidenceService;
use Illuminate\Contracts\View\View;

final class ConfigurationReleaseEvidenceController extends Controller
{
    /**
     * Show release evidence for app, auth, and quality-gate configuration work.
     */
    public function __invoke(ConfigurationReleaseEvidenceService $releaseEvidence): View
    {
        return view('practice.configuration-release-evidence', [
            'releaseEvidence' => $releaseEvidence->build(),
        ]);
    }
}
