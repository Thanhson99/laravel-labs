<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationDecisionRecordService;
use Illuminate\Contracts\View\View;

final class ConfigurationDecisionRecordController extends Controller
{
    /**
     * Show the ADR-style decision record for configuration practice.
     */
    public function __invoke(ConfigurationDecisionRecordService $decisionRecord): View
    {
        return view('practice.configuration-decision-record', [
            'decisionRecord' => $decisionRecord->build(),
        ]);
    }
}
