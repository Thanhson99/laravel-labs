<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeMasteryPathLabService;
use Illuminate\Contracts\View\View;

final class PracticeMasteryPathLabController extends Controller
{
    /**
     * Show a multi-technology mastery path.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeMasteryPathLabService $paths): View
    {
        $filters = $request->phasedFilters(defaultPhaseLimit: 3);

        return view('practice.mastery-path-lab', [
            'filters' => $filters,
            'path' => $paths->build($filters),
        ]);
    }
}
