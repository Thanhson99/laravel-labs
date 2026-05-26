<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationOperationsRunbookService;
use Illuminate\Contracts\View\View;

final class ConfigurationOperationsRunbookController extends Controller
{
    /**
     * Show the operations runbook for configuration practice.
     */
    public function __invoke(ConfigurationOperationsRunbookService $runbook): View
    {
        return view('practice.configuration-operations-runbook', [
            'runbook' => $runbook->build(),
        ]);
    }
}
