<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationRiskRegisterService;
use Illuminate\Contracts\View\View;

final class ConfigurationRiskRegisterController extends Controller
{
    /**
     * Show the risk register for app, auth, and quality-gate configuration practice.
     */
    public function __invoke(ConfigurationRiskRegisterService $riskRegister): View
    {
        return view('practice.configuration-risk-register', [
            'riskRegister' => $riskRegister->build(),
        ]);
    }
}
