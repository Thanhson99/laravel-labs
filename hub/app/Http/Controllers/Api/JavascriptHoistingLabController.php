<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnalyzeJavascriptHoistingRequest;
use App\Services\Practice\JavascriptHoistingLabService;
use Illuminate\Http\JsonResponse;

final class JavascriptHoistingLabController extends Controller
{
    /**
     * Return a JavaScript hoisting analysis for interview practice.
     */
    public function __invoke(AnalyzeJavascriptHoistingRequest $request, JavascriptHoistingLabService $lab): JsonResponse
    {
        return $this->jsonData($lab->analyze($request->analysisData()));
    }
}
