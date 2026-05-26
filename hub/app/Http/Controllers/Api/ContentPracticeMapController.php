<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\ContentPracticeMapperService;
use Illuminate\Http\JsonResponse;

final class ContentPracticeMapController extends Controller
{
    /**
     * Return content-to-practice mappings for JSON records.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, ContentPracticeMapperService $mapper): JsonResponse
    {
        return $this->jsonData($mapper->map($request->optionalTechnologyFilters(
            defaultLimit: 12,
            defaultFamily: null,
            defaultLanguage: null,
            defaultSearch: null
        )));
    }
}
