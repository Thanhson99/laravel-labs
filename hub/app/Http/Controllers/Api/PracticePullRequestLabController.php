<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\PracticePullRequestLabService;
use Illuminate\Http\JsonResponse;

final class PracticePullRequestLabController extends Controller
{
    /**
     * Return pull-request artifacts for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, PracticePullRequestLabService $pullRequests): JsonResponse
    {
        $pullRequest = $pullRequests->build($request->contentFilters());

        return $this->jsonDataOrNotFound($pullRequest, 'Practice pull request lab not found.');
    }
}
