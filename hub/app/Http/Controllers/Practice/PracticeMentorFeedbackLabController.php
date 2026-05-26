<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\PracticeTechnologyFilterRequest;
use App\Services\Practice\PracticeMentorFeedbackLabService;
use Illuminate\Contracts\View\View;

final class PracticeMentorFeedbackLabController extends Controller
{
    /**
     * Show mentor-style feedback for a technology capstone.
     */
    public function __invoke(PracticeTechnologyFilterRequest $request, PracticeMentorFeedbackLabService $feedback): View
    {
        $filters = $request->requiredTechnologyFilters(defaultLimit: 3);

        return view('practice.mentor-feedback-lab', [
            'filters' => $filters,
            'feedback' => $feedback->build($filters),
        ]);
    }
}
