<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\SourcePracticePackService;
use Illuminate\Http\JsonResponse;

final class SourcePracticePackController extends Controller
{
    /**
     * Return one source file as a practice pack.
     */
    public function __invoke(string $sourceKey, SourcePracticePackService $packs): JsonResponse
    {
        $pack = $packs->build($sourceKey);

        return $this->jsonDataOrNotFound($pack, 'Source practice pack not found.');
    }
}
