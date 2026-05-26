<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationMasteryCheckpointService;
use Illuminate\Contracts\View\View;

final class ConfigurationMasteryCheckpointController extends Controller
{
    /**
     * Show the promote-or-repeat checkpoint for configuration practice.
     */
    public function __invoke(ConfigurationMasteryCheckpointService $checkpoint): View
    {
        return view('practice.configuration-mastery-checkpoint', [
            'checkpoint' => $checkpoint->build(),
        ]);
    }
}
