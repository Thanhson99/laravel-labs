<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\QuestionDrillSetService;
use Illuminate\Contracts\View\View;

final class QuestionDrillSetController extends Controller
{
    /**
     * Show question records as code-first drill cards.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, QuestionDrillSetService $drills): View
    {
        $filters = $request->optionalTechnologyFilters(defaultLimit: 8);

        return view('practice.question-drills', [
            'filters' => $filters,
            'drills' => $drills->build($filters),
        ]);
    }
}
