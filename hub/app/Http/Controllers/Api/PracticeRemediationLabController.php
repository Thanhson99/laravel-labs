<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticeRemediationLabService;
use Illuminate\Http\JsonResponse;

final class PracticeRemediationLabController extends Controller
{
    /**
     * Return remediation tasks for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticeRemediationLabService $remediation): JsonResponse
    {
        $lab = $remediation->build($request->contentFilters());

        return $this->jsonDataOrNotFound($lab, 'Practice remediation lab not found.');
    }
}
