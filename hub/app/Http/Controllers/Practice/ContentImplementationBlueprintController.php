<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ContentImplementationBlueprintService;
use Illuminate\Contracts\View\View;

final class ContentImplementationBlueprintController extends Controller
{
    /**
     * Show concrete implementation names for one content-backed drill.
     */
    public function __invoke(PracticeContentFilterRequest $request, ContentImplementationBlueprintService $blueprints): View
    {
        $filters = $request->contentFilters();

        return view('practice.implementation-blueprint', [
            'blueprint' => $blueprints->build($filters),
            'filters' => $filters,
        ]);
    }
}
