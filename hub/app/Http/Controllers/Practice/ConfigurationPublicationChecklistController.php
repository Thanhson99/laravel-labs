<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationPublicationChecklistService;
use Illuminate\Contracts\View\View;

final class ConfigurationPublicationChecklistController extends Controller
{
    /**
     * Show the publication checklist for configuration evidence.
     */
    public function __invoke(ConfigurationPublicationChecklistService $checklist): View
    {
        return view('practice.configuration-publication-checklist', [
            'checklist' => $checklist->build(),
        ]);
    }
}
