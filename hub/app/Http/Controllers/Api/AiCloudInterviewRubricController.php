<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ScoreAiCloudInterviewRubricRequest;
use App\Services\Practice\AiCloudInterviewRubricService;
use Illuminate\Http\JsonResponse;

final class AiCloudInterviewRubricController extends Controller
{
    /**
     * Return a scored rubric for practical AI usage in Cloud interviews.
     */
    public function __invoke(ScoreAiCloudInterviewRubricRequest $request, AiCloudInterviewRubricService $rubric): JsonResponse
    {
        return $this->jsonData($rubric->score($request->rubricData()));
    }
}
