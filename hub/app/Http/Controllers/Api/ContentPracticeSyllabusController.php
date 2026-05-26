<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\ContentPracticeSyllabusService;
use Illuminate\Http\JsonResponse;

final class ContentPracticeSyllabusController extends Controller
{
    /**
     * Return a content-backed practice syllabus.
     */
    public function __invoke(PracticeSourceFilterRequest $request, ContentPracticeSyllabusService $syllabus): JsonResponse
    {
        return $this->jsonData($syllabus->build($request->sourceFilters(
            defaultFamily: 'laravel',
            defaultLanguage: 'en',
            defaultLimit: 10
        )));
    }
}
