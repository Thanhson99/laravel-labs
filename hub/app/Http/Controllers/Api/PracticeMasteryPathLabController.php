<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeLabFilterRequest;
use App\Services\Practice\PracticeMasteryPathLabService;
use Illuminate\Http\JsonResponse;

final class PracticeMasteryPathLabController extends Controller
{
    /**
     * Return a multi-technology mastery path.
     */
    public function __invoke(PracticeLabFilterRequest $request, PracticeMasteryPathLabService $paths): JsonResponse
    {
        return $this->jsonData($paths->build($request->phasedFilters(defaultPhaseLimit: 3)));
    }
}
