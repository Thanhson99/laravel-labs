<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyPipelineIndexService;
use Illuminate\Http\JsonResponse;

final class TechnologyPipelineIndexController extends Controller
{
    /**
     * Return the discoverable index of technology learning pipelines.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyPipelineIndexService $pipelines): JsonResponse
    {
        return $this->jsonData($pipelines->build($request->sourceFilters(defaultLanguage: 'en')));
    }
}
