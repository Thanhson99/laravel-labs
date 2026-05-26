<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeRetrospectiveLabService;
use Illuminate\Http\JsonResponse;

final class PracticeRetrospectiveLabController extends Controller
{
    /**
     * Return retrospective prompts for one assessed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeRetrospectiveLabService $retrospectives): JsonResponse
    {
        $retrospective = $retrospectives->build($request->contentFilters());

        return $this->jsonDataOrNotFound($retrospective, 'Practice retrospective lab not found.');
    }
}
