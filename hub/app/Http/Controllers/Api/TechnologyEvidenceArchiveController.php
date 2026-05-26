<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyEvidenceArchiveService;
use Illuminate\Http\JsonResponse;

final class TechnologyEvidenceArchiveController extends Controller
{
    /**
     * Return an evidence archive entry for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyEvidenceArchiveService $archives
    ): JsonResponse {
        return $this->jsonData($archives->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
