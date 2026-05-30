<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReviewIdorAccessRequest;
use App\Services\Practice\IdorAccessReviewService;
use Illuminate\Http\JsonResponse;

final class IdorAccessReviewController extends Controller
{
    /**
     * Return an IDOR access review for object-level authorization practice.
     */
    public function __invoke(ReviewIdorAccessRequest $request, IdorAccessReviewService $reviewer): JsonResponse
    {
        return response()->json([
            'data' => $reviewer->review($request->reviewData()),
        ]);
    }
}
