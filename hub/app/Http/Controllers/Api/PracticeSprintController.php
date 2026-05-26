<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeSprintService;
use Illuminate\Http\JsonResponse;

final class PracticeSprintController extends Controller
{
    /**
     * Return a content-backed sprint of concrete Laravel practice tasks.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeSprintService $sprints): JsonResponse
    {
        return $this->jsonData($sprints->build($request->phasedFilters()));
    }
}
