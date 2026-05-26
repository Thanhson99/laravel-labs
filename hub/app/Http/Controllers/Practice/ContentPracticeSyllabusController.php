<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeSourceFilterRequest;
use App\Services\Practice\ContentPracticeSyllabusService;
use Illuminate\Contracts\View\View;

final class ContentPracticeSyllabusController extends Controller
{
    /**
     * Show a content-backed practice syllabus.
     */
    public function __invoke(PracticeSourceFilterRequest $request, ContentPracticeSyllabusService $syllabus): View
    {
        $filters = $request->sourceFilters(defaultFamily: 'laravel', defaultLanguage: 'en', defaultLimit: 10);

        return view('practice.syllabus', [
            'filters' => $filters,
            'syllabus' => $syllabus->build($filters),
        ]);
    }
}
