<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSessionFilterRequest;
use App\Services\Practice\PracticeSessionPlannerService;
use Illuminate\Http\JsonResponse;

final class PracticeSessionPlanController extends Controller
{
    /**
     * Return a native practice session plan.
     */
    public function __invoke(PracticeSessionFilterRequest $request, PracticeSessionPlannerService $planner): JsonResponse
    {
        $track = $request->selectedTrack();

        return $this->jsonDataWithMeta($planner->today($track), [
            'track' => $track,
        ]);
    }
}
