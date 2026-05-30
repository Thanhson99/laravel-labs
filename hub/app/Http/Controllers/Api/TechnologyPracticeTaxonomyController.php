<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Practice\TechnologyPracticeTaxonomyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TechnologyPracticeTaxonomyController extends Controller
{
    /**
     * Return supported technology keys with practice and workbench links.
     */
    public function __invoke(Request $request, TechnologyPracticeTaxonomyService $taxonomy): JsonResponse
    {
        return $this->jsonData($taxonomy->build([
            'search' => $request->query('search'),
            'strength' => $request->query('strength'),
        ]));
    }
}
