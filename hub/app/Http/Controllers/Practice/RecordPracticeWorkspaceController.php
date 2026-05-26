<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\RecordPracticeWorkspaceService;
use Illuminate\Contracts\View\View;

final class RecordPracticeWorkspaceController extends Controller
{
    /**
     * Show the complete practice workspace for one JSON content record.
     */
    public function __invoke(PracticeContentFilterRequest $request, RecordPracticeWorkspaceService $workspaces): View
    {
        $filters = $request->contentFilters();

        return view('practice.record-workspace', [
            'workspace' => $workspaces->build($filters),
            'filters' => $filters,
        ]);
    }
}
