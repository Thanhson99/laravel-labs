<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\TechnologyPracticeTaxonomyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class TechnologyPracticeTaxonomyController extends Controller
{
    /**
     * Show supported technology keys with practice and workbench links.
     */
    public function __invoke(Request $request, TechnologyPracticeTaxonomyService $taxonomy): View
    {
        $filters = [
            'search' => $request->query('search'),
            'strength' => $request->query('strength'),
        ];

        return view('practice.technology-taxonomy', [
            'taxonomy' => $taxonomy->build($filters),
            'filters' => $filters,
        ]);
    }
}
