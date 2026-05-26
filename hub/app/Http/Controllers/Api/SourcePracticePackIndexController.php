<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\SourcePracticePackIndexService;
use Illuminate\Http\JsonResponse;

final class SourcePracticePackIndexController extends Controller
{
    /**
     * Return JSON sources that can be opened as practice packs.
     */
    public function __invoke(PracticeSourceFilterRequest $request, SourcePracticePackIndexService $index): JsonResponse
    {
        return $this->jsonData($index->build($request->sourceFilters(defaultLanguage: 'en', defaultLimit: 20)));
    }
}
