<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ContentPracticeDrillService;
use Illuminate\Contracts\View\View;

final class ContentPracticeDrillController extends Controller
{
    /**
     * Show one focused content-backed practice drill.
     */
    public function __invoke(PracticeContentFilterRequest $request, ContentPracticeDrillService $drills): View
    {
        $filters = $request->contentFilters();

        return view('practice.content-drill', [
            'drill' => $drills->build($filters),
            'filters' => $filters,
        ]);
    }
}
