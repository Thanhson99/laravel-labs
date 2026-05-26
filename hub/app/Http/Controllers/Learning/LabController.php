<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LearningContentRepositoryInterface;
use App\Services\Learning\LearningLabService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class LabController extends Controller
{
    /**
     * Show generated hands-on practice labs.
     */
    public function __invoke(
        Request $request,
        LearningLabService $labs,
        LearningContentRepositoryInterface $content,
    ): View {
        $filters = [
            'language' => $request->string('language')->toString() ?: 'en',
            'family' => $request->string('family')->toString() ?: null,
            'track' => $request->string('track')->toString() ?: null,
            'search' => $request->string('search')->toString() ?: null,
            'limit' => $request->integer('limit') ?: null,
        ];

        return view('learning.labs', [
            'filters' => $filters,
            'families' => collect($content->sources())->pluck('family')->unique()->sort()->values()->all(),
            'languages' => collect($content->sources())->pluck('language')->filter()->unique()->sort()->values()->all(),
            'tracks' => $labs->tracks(),
            'labs' => $labs->build($filters),
        ]);
    }
}
