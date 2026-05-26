<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyCodeExampleService;
use Illuminate\Http\JsonResponse;

final class TechnologyCodeExampleController extends Controller
{
    /**
     * Return technology-specific code examples generated from JSON content.
     */
    public function __invoke(PracticeSourceFilterRequest $request, TechnologyCodeExampleService $examples): JsonResponse
    {
        return $this->jsonData($examples->build($request->sourceFilters(defaultLanguage: 'en', defaultLimit: 50)));
    }
}
