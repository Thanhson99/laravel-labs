<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeQueueService;
use Illuminate\Contracts\View\View;

final class PracticeQueueController extends Controller
{
    /**
     * Show an ordered queue of record-level practice workspaces.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeQueueService $queues): View
    {
        $filters = $request->optionalTechnologyFilters(defaultLimit: 5);

        return view('practice.queue', [
            'filters' => $filters,
            'queue' => $queues->build($filters),
        ]);
    }
}
