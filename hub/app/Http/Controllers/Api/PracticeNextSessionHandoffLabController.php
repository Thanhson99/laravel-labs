<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeNextSessionHandoffLabService;
use Illuminate\Http\JsonResponse;

final class PracticeNextSessionHandoffLabController extends Controller
{
    /**
     * Return next-session handoff cards generated from promotion decisions.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeNextSessionHandoffLabService $handoffs): JsonResponse
    {
        return $this->jsonData($handoffs->build($request->pipelineFilters()));
    }
}
