<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EvaluateQualityGateRequest;
use App\Services\Practice\PracticeQualityGateService;
use Illuminate\Http\JsonResponse;

final class PracticeQualityGateController extends Controller
{
    /**
     * Evaluate whether a practice task is ready to submit.
     */
    public function store(EvaluateQualityGateRequest $request, PracticeQualityGateService $qualityGate): JsonResponse
    {
        return $this->jsonData($qualityGate->evaluate($request->results()));
    }
}
