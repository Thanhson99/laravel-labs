<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeCapstoneLabService;
use Illuminate\Contracts\View\View;

final class PracticeCapstoneLabController extends Controller
{
    /**
     * Show a technology-level capstone lab.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeCapstoneLabService $capstones): View
    {
        $filters = $request->requiredTechnologyFilters(defaultLimit: 3);

        return view('practice.capstone-lab', [
            'filters' => $filters,
            'capstone' => $capstones->build($filters),
        ]);
    }
}
