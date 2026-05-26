<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationChangeChecklistService;
use Illuminate\Contracts\View\View;

final class ConfigurationChangeChecklistController extends Controller
{
    /**
     * Show a review checklist for app, auth, and quality-gate configuration changes.
     */
    public function __invoke(ConfigurationChangeChecklistService $checklist): View
    {
        return view('practice.configuration-change-checklist', [
            'checklist' => $checklist->build(),
        ]);
    }
}
