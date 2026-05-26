<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeCapstoneLabService;
use Illuminate\Http\JsonResponse;

final class PracticeCapstoneLabController extends Controller
{
    /**
     * Return a technology-level capstone lab.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeCapstoneLabService $capstones): JsonResponse
    {
        return $this->jsonData($capstones->build($request->requiredTechnologyFilters(defaultLimit: 3)));
    }
}
