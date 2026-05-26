<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeQueueService;
use Illuminate\Http\JsonResponse;

final class PracticeQueueController extends Controller
{
    /**
     * Return an ordered queue of record-level practice workspaces.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeQueueService $queues): JsonResponse
    {
        return $this->jsonData($queues->build($request->optionalTechnologyFilters(defaultLimit: 5)));
    }
}
