<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\ContentPracticeMapperService;
use Illuminate\Contracts\View\View;

final class ContentPracticeMapController extends Controller
{
    /**
     * Show how JSON learning content maps to native Laravel practice.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, ContentPracticeMapperService $mapper): View
    {
        $filters = $request->optionalTechnologyFilters(
            defaultLimit: 12,
            defaultFamily: null,
            defaultLanguage: null,
            defaultSearch: null
        );

        return view('practice.content-map', [
            'result' => $mapper->map($filters),
            'filters' => $filters,
        ]);
    }
}
