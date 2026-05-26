<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeTddLabService;
use Illuminate\Contracts\View\View;

final class PracticeTddLabController extends Controller
{
    /**
     * Show a Red-Green-Refactor lab for one content-backed record.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeTddLabService $labs): View
    {
        $filters = $request->contentFilters();

        return view('practice.tdd-lab', [
            'filters' => $filters,
            'lab' => $labs->build($filters),
        ]);
    }
}
