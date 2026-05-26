<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyInterviewPackService;
use Illuminate\Contracts\View\View;

final class TechnologyInterviewPackController extends Controller
{
    /**
     * Show interview defense questions for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyInterviewPackService $packs
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-interview-pack', [
            'pack' => $packs->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
