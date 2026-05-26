<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ContentPracticeDrillService;
use Illuminate\Http\JsonResponse;

final class ContentPracticeDrillController extends Controller
{
    /**
     * Return one focused content-backed practice drill.
     */
    public function __invoke(PracticeContentFilterRequest $request, ContentPracticeDrillService $drills): JsonResponse
    {
        $drill = $drills->build($request->contentFilters());

        return $this->jsonDataOrNotFound($drill, 'No content practice drill matched the requested filters.');
    }
}
