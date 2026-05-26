<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationEvidenceArchiveService;
use Illuminate\Contracts\View\View;

final class ConfigurationEvidenceArchiveController extends Controller
{
    /**
     * Show archived evidence for configuration practice.
     */
    public function __invoke(ConfigurationEvidenceArchiveService $archive): View
    {
        return view('practice.configuration-evidence-archive', [
            'archive' => $archive->build(),
        ]);
    }
}
