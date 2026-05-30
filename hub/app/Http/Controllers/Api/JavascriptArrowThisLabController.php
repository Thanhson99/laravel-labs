<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnalyzeJavascriptArrowThisRequest;
use App\Services\Practice\JavascriptArrowThisLabService;
use Illuminate\Http\JsonResponse;

final class JavascriptArrowThisLabController extends Controller
{
    /**
     * Return a JavaScript arrow-function this analysis for interview practice.
     */
    public function __invoke(AnalyzeJavascriptArrowThisRequest $request, JavascriptArrowThisLabService $lab): JsonResponse
    {
        return $this->jsonData($lab->analyze($request->analysisData()));
    }
}
