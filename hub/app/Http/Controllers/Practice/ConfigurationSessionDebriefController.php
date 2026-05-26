<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationSessionDebriefService;
use Illuminate\Contracts\View\View;

final class ConfigurationSessionDebriefController extends Controller
{
    /**
     * Show the debrief for a configuration follow-up session.
     */
    public function __invoke(ConfigurationSessionDebriefService $debrief): View
    {
        return view('practice.configuration-session-debrief', [
            'debrief' => $debrief->build(),
        ]);
    }
}
