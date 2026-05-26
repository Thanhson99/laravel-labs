<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\RecordPracticeWorkspaceService;
use Illuminate\Http\JsonResponse;

final class RecordPracticeWorkspaceController extends Controller
{
    /**
     * Return the complete practice workspace for one JSON content record.
     */
    public function __invoke(PracticeContentFilterRequest $request, RecordPracticeWorkspaceService $workspaces): JsonResponse
    {
        $workspace = $workspaces->build($request->contentFilters());

        return $this->jsonDataOrNotFound($workspace, 'Record practice workspace not found.');
    }
}
