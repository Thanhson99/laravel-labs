<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\QuestionDrillSetService;
use Illuminate\Http\JsonResponse;

final class QuestionDrillSetController extends Controller
{
    /**
     * Return question-backed coding drill cards.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, QuestionDrillSetService $drills): JsonResponse
    {
        return $this->jsonData($drills->build($request->optionalTechnologyFilters(defaultLimit: 8)));
    }
}
