<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ContentImplementationBlueprintService;
use Illuminate\Http\JsonResponse;

final class ContentImplementationBlueprintController extends Controller
{
    /**
     * Return concrete implementation names for one content-backed drill.
     */
    public function __invoke(PracticeContentFilterRequest $request, ContentImplementationBlueprintService $blueprints): JsonResponse
    {
        $blueprint = $blueprints->build($request->contentFilters());

        return $this->jsonDataOrNotFound($blueprint, 'Implementation blueprint not found.');
    }
}
