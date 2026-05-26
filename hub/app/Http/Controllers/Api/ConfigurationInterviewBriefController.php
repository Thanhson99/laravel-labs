<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationInterviewBriefService;
use Illuminate\Http\JsonResponse;

final class ConfigurationInterviewBriefController extends Controller
{
    /**
     * Return interview questions for app, auth, and quality-gate configuration work.
     */
    public function __invoke(ConfigurationInterviewBriefService $interviewBrief): JsonResponse
    {
        return $this->jsonData($interviewBrief->build());
    }
}
