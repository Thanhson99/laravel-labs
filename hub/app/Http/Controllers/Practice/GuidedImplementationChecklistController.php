<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\GuidedImplementationChecklistService;
use Illuminate\Contracts\View\View;

final class GuidedImplementationChecklistController extends Controller
{
    /**
     * Show a guided implementation checklist for one content-backed blueprint.
     */
    public function __invoke(PracticeContentFilterRequest $request, GuidedImplementationChecklistService $checklists): View
    {
        $filters = $request->contentFilters();

        return view('practice.guided-checklist', [
            'checklist' => $checklists->build($filters),
            'filters' => $filters,
        ]);
    }
}
