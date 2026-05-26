<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice\Workbench;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\NameNormalizerWorkbenchRequest;
use App\Http\Requests\Practice\NormalizeNamesRequest;
use App\Practice\Php\NameNormalizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class NameNormalizerController extends Controller
{
    /**
     * Show a runnable PHP foundation workbench.
     */
    public function index(NameNormalizerWorkbenchRequest $request, NameNormalizer $normalizer): View
    {
        $rawInput = $request->rawNames();
        $names = preg_split('/\r\n|\r|\n/', $rawInput) ?: [];

        return view('practice.workbench.name-normalizer', [
            'rawInput' => $rawInput,
            'normalizedNames' => $normalizer->normalizeMany(array_map('strval', $names)),
        ]);
    }

    /**
     * Normalize names through a JSON endpoint for API practice.
     */
    public function store(NormalizeNamesRequest $request, NameNormalizer $normalizer): JsonResponse
    {
        $normalizedNames = $normalizer->normalizeMany($request->names());

        return $this->jsonDataWithMeta([
            'names' => $normalizedNames,
        ], [
            'submitted' => count($request->names()),
            'accepted' => count($normalizedNames),
        ]);
    }
}
