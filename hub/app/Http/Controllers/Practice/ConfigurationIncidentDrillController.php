<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationIncidentDrillService;
use Illuminate\Contracts\View\View;

final class ConfigurationIncidentDrillController extends Controller
{
    /**
     * Show the incident drill for configuration practice.
     */
    public function __invoke(ConfigurationIncidentDrillService $incidentDrill): View
    {
        return view('practice.configuration-incident-drill', [
            'incidentDrill' => $incidentDrill->build(),
        ]);
    }
}
