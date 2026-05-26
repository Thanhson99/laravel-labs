<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ImplementationVerificationPlanService;
use Illuminate\Http\JsonResponse;

final class ImplementationVerificationPlanController extends Controller
{
    /**
     * Return verification commands for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, ImplementationVerificationPlanService $plans): JsonResponse
    {
        $plan = $plans->build($request->contentFilters());

        return $this->jsonDataOrNotFound($plan, 'Implementation verification plan not found.');
    }
}
