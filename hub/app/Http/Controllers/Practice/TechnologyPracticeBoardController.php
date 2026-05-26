<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\TechnologyPracticeBoardService;
use Illuminate\Contracts\View\View;

final class TechnologyPracticeBoardController extends Controller
{
    /**
     * Show source groups and workspace links for one technology.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, TechnologyPracticeBoardService $boards): View
    {
        $filters = $request->requiredTechnologyFilters(defaultLimit: 50, defaultSearch: null);

        return view('practice.technology-board', [
            'filters' => $filters,
            'board' => $boards->build($filters),
        ]);
    }
}
