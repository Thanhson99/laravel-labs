<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeCatalogFilterRequest;
use App\Services\Practice\PracticeCatalogService;
use Illuminate\Contracts\View\View;

final class PracticeController extends Controller
{
    /**
     * Show the native Laravel practice workspace.
     */
    public function index(PracticeCatalogFilterRequest $request, PracticeCatalogService $catalog): View
    {
        $filters = $request->catalogFilters();

        return view('practice.index', [
            'filters' => $filters,
            'tracks' => $catalog->tracks(),
            'exercises' => $catalog->exercises($filters),
        ]);
    }

    /**
     * Show one practice exercise.
     */
    public function show(string $exercise, PracticeCatalogService $catalog): View
    {
        $practice = $catalog->findExercise($exercise);

        abort_if($practice === null, 404);

        return view('practice.show', [
            'exercise' => $practice,
            'track' => $catalog->findTrack($practice['track']),
        ]);
    }
}
