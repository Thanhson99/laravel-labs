<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeCheckpointExamLabService;
use Illuminate\Contracts\View\View;

final class PracticeCheckpointExamLabController extends Controller
{
    /**
     * Show a timed checkpoint exam for one technology.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeCheckpointExamLabService $exams): View
    {
        $filters = $request->requiredTechnologyFilters(defaultLimit: 2);

        return view('practice.checkpoint-exam-lab', [
            'exam' => $exams->build($filters),
            'filters' => $filters,
        ]);
    }
}
