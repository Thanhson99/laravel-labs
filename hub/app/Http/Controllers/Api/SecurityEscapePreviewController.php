<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PreviewEscapedContentRequest;
use App\Services\Practice\SecurityEscapePreviewService;
use Illuminate\Http\JsonResponse;

final class SecurityEscapePreviewController extends Controller
{
    /**
     * Return escaped preview data for learner-provided content.
     */
    public function __invoke(PreviewEscapedContentRequest $request, SecurityEscapePreviewService $previewer): JsonResponse
    {
        return $this->jsonData($previewer->preview($request->content()));
    }
}
