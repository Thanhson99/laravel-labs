<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeCompetencyMapLabService;
use Illuminate\Http\JsonResponse;

final class PracticeCompetencyMapLabController extends Controller
{
    /**
     * Return competency maps generated from mastery evidence.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeCompetencyMapLabService $maps): JsonResponse
    {
        return $this->jsonData($maps->build($request->pipelineFilters()));
    }
}
