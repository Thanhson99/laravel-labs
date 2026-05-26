<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationIncidentPostmortemService;
use Illuminate\Contracts\View\View;

final class ConfigurationIncidentPostmortemController extends Controller
{
    /**
     * Show the postmortem for configuration incident practice.
     */
    public function __invoke(ConfigurationIncidentPostmortemService $postmortem): View
    {
        return view('practice.configuration-incident-postmortem', [
            'postmortem' => $postmortem->build(),
        ]);
    }
}
