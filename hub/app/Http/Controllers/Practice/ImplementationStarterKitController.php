<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeContentFilterRequest;
use App\Services\Practice\ImplementationStarterKitService;
use Illuminate\Contracts\View\View;

final class ImplementationStarterKitController extends Controller
{
    /**
     * Show starter snippets for one content-backed implementation.
     */
    public function __invoke(PracticeContentFilterRequest $request, ImplementationStarterKitService $starterKits): View
    {
        $filters = $request->contentFilters();

        return view('practice.starter-kit', [
            'starterKit' => $starterKits->build($filters),
            'filters' => $filters,
        ]);
    }
}
