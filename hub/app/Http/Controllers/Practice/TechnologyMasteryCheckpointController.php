<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyMasteryCheckpointService;
use Illuminate\Contracts\View\View;

final class TechnologyMasteryCheckpointController extends Controller
{
    /**
     * Show a promote-or-repeat checkpoint for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyMasteryCheckpointService $checkpoints
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-mastery-checkpoint', [
            'checkpoint' => $checkpoints->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
