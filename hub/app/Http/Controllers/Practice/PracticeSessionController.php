<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSessionFilterRequest;
use App\Services\Practice\PracticeCatalogService;
use App\Services\Practice\PracticeSessionPlannerService;
use Illuminate\Contracts\View\View;

final class PracticeSessionController extends Controller
{
    /**
     * Show the current code-first practice session.
     */
    public function __invoke(
        PracticeSessionFilterRequest $request,
        PracticeSessionPlannerService $planner,
        PracticeCatalogService $catalog,
    ): View {
        $track = $request->selectedTrack();

        return view('practice.session', [
            'plan' => $planner->today($track),
            'tracks' => $catalog->tracks(),
            'selectedTrack' => $track,
        ]);
    }
}
