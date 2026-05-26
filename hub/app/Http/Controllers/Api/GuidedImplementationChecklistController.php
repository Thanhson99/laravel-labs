<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\GuidedImplementationChecklistService;
use Illuminate\Http\JsonResponse;

final class GuidedImplementationChecklistController extends Controller
{
    /**
     * Return a guided implementation checklist for one content-backed blueprint.
     */
    public function __invoke(PracticeContentFilterRequest $request, GuidedImplementationChecklistService $checklists): JsonResponse
    {
        $checklist = $checklists->build($request->contentFilters());

        return $this->jsonDataOrNotFound($checklist, 'Guided implementation checklist not found.');
    }
}
