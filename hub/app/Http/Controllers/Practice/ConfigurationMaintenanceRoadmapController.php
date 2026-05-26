<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationMaintenanceRoadmapService;
use Illuminate\Contracts\View\View;

final class ConfigurationMaintenanceRoadmapController extends Controller
{
    /**
     * Show the maintenance roadmap for configuration evidence.
     */
    public function __invoke(ConfigurationMaintenanceRoadmapService $roadmap): View
    {
        return view('practice.configuration-maintenance-roadmap', [
            'roadmap' => $roadmap->build(),
        ]);
    }
}
