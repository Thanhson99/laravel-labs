<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PreviewCollectionFilterRequest;
use App\Services\Practice\CollectionFilterPreviewService;
use Illuminate\Http\JsonResponse;

final class CollectionFilterPreviewController extends Controller
{
    /**
     * Return filtered and paginated preview records for list-page practice.
     */
    public function __invoke(PreviewCollectionFilterRequest $request, CollectionFilterPreviewService $previewer): JsonResponse
    {
        return $this->jsonData($previewer->preview($request->filters()));
    }
}
