<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeWeeklyReportLabService;
use Illuminate\Http\JsonResponse;

final class PracticeWeeklyReportLabController extends Controller
{
    /**
     * Return a weekly practice report.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeWeeklyReportLabService $reports): JsonResponse
    {
        return $this->jsonData($reports->build($request->pipelineFilters()));
    }
}
