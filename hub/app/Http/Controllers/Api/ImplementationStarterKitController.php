<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ImplementationStarterKitService;
use Illuminate\Http\JsonResponse;

final class ImplementationStarterKitController extends Controller
{
    /**
     * Return starter snippets for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, ImplementationStarterKitService $starterKits): JsonResponse
    {
        $starterKit = $starterKits->build($request->contentFilters());

        return $this->jsonDataOrNotFound($starterKit, 'Implementation starter kit not found.');
    }
}
