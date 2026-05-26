<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyPracticeMatrixService;
use Illuminate\Http\JsonResponse;

final class TechnologyPracticeMatrixController extends Controller
{
    /**
     * Return content coverage by technology with native practice links.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyPracticeMatrixService $matrix): JsonResponse
    {
        return $this->jsonData($matrix->build($request->sourceFilters(defaultLanguage: 'en')));
    }
}
