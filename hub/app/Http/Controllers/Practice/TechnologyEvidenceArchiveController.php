<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\TechnologyEvidenceArchiveService;
use Illuminate\Contracts\View\View;

final class TechnologyEvidenceArchiveController extends Controller
{
    /**
     * Show an evidence archive entry for one inferred technology.
     */
    public function __invoke(
        string $technology,
        PracticeSourceFilterRequest $request,
        TechnologyEvidenceArchiveService $archives
    ): View {
        $filters = $request->sourceFilters(defaultLanguage: 'en', defaultLimit: 5);

        return view('practice.technology-evidence-archive', [
            'archive' => $archives->build($technology, $filters),
            'filters' => $filters,
        ]);
    }
}
