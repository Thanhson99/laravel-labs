<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeDemoScriptLabService;
use Illuminate\Http\JsonResponse;

final class PracticeDemoScriptLabController extends Controller
{
    /**
     * Return a demo script for a weekly practice report.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeDemoScriptLabService $scripts): JsonResponse
    {
        return $this->jsonData($scripts->build($request->pipelineFilters()));
    }
}
