<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeRefactorLabService;
use Illuminate\Http\JsonResponse;

final class PracticeRefactorLabController extends Controller
{
    /**
     * Return refactor tasks generated from bug-fix drills.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeRefactorLabService $refactors): JsonResponse
    {
        return $this->jsonData($refactors->build($request->pipelineFilters()));
    }
}
