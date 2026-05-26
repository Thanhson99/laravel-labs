<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TraceHttpRequestFlowRequest;
use App\Services\Practice\HttpRequestFlowTracerService;
use Illuminate\Http\JsonResponse;

final class HttpRequestFlowTraceController extends Controller
{
    /**
     * Return a readable Laravel request-flow trace for learner input.
     */
    public function __invoke(TraceHttpRequestFlowRequest $request, HttpRequestFlowTracerService $tracer): JsonResponse
    {
        $trace = $request->traceData();

        return $this->jsonData($tracer->trace(
            $trace['method'],
            $trace['path'],
            $trace['accept'],
        ));
    }
}
