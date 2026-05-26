<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\TechnologyPracticeBoardService;
use Illuminate\Http\JsonResponse;

final class TechnologyPracticeBoardController extends Controller
{
    /**
     * Return source groups and workspace links for one technology.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, TechnologyPracticeBoardService $boards): JsonResponse
    {
        return $this->jsonData($boards->build($request->requiredTechnologyFilters(defaultLimit: 50, defaultSearch: null)));
    }
}
