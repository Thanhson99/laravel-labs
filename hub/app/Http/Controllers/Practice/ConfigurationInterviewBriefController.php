<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationInterviewBriefService;
use Illuminate\Contracts\View\View;

final class ConfigurationInterviewBriefController extends Controller
{
    /**
     * Show interview questions for app, auth, and quality-gate configuration work.
     */
    public function __invoke(ConfigurationInterviewBriefService $interviewBrief): View
    {
        return view('practice.configuration-interview-brief', [
            'interviewBrief' => $interviewBrief->build(),
        ]);
    }
}
