<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeTddLabService;
use Illuminate\Http\JsonResponse;

final class PracticeTddLabController extends Controller
{
    /**
     * Return a Red-Green-Refactor lab for one content-backed record.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeTddLabService $labs): JsonResponse
    {
        $lab = $labs->build($request->contentFilters());

        return $this->jsonDataOrNotFound($lab, 'Practice TDD lab not found.');
    }
}
