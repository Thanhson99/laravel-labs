<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeCatalogFilterRequest;
use App\Services\Practice\PracticeCatalogService;
use Illuminate\Http\JsonResponse;

final class PracticeCatalogApiController extends Controller
{
    /**
     * Return practice tracks and exercises.
     */
    public function index(PracticeCatalogFilterRequest $request, PracticeCatalogService $catalog): JsonResponse
    {
        $filters = $request->catalogFilters();

        return $this->jsonDataWithMeta([
            'tracks' => $catalog->tracks(),
            'exercises' => $catalog->exercises($filters),
        ], [
            'filters' => $filters,
        ]);
    }

    /**
     * Return one configured practice exercise.
     */
    public function show(string $exercise, PracticeCatalogService $catalog): JsonResponse
    {
        $practice = $catalog->findExercise($exercise);

        return $this->jsonDataOrNotFound($practice, 'Practice exercise not found.');
    }
}
