<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanFileStorageRequest;
use App\Services\Practice\FileStoragePlanService;
use Illuminate\Http\JsonResponse;

final class FileStoragePlanController extends Controller
{
    /**
     * Return a file storage plan for upload and media practice.
     */
    public function __invoke(PlanFileStorageRequest $request, FileStoragePlanService $planner): JsonResponse
    {
        return $this->jsonData($planner->plan($request->planData()));
    }
}
