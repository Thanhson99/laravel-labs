<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\SourcePracticePackIndexService;
use Illuminate\Contracts\View\View;

final class SourcePracticePackIndexController extends Controller
{
    /**
     * Show JSON sources that can be opened as practice packs.
     */
    public function __invoke(PracticeSourceFilterRequest $request, SourcePracticePackIndexService $index): View
    {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 20);

        return view('practice.source-pack-index', [
            'filters' => $filters,
            'index' => $index->build($filters),
        ]);
    }
}
