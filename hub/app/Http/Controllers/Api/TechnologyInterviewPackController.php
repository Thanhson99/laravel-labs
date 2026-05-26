<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyInterviewPackService;
use Illuminate\Http\JsonResponse;

final class TechnologyInterviewPackController extends Controller
{
    /**
     * Return interview defense questions for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyInterviewPackService $packs
    ): JsonResponse {
        return $this->jsonData($packs->build(
            $technology,
            $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5),
        ));
    }
}
