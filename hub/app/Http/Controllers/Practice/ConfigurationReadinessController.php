<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationReadinessService;
use Illuminate\Contracts\View\View;

final class ConfigurationReadinessController extends Controller
{
    /**
     * Show a read-only readiness report for app and auth configuration.
     */
    public function __invoke(ConfigurationReadinessService $readiness): View
    {
        return view('practice.configuration-readiness', [
            'readiness' => $readiness->build(),
        ]);
    }
}
