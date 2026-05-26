<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class QuestionBankController extends Controller
{
    /**
     * Show the searchable JSON-backed question bank.
     */
    public function __invoke(Request $request, LearningContentRepositoryInterface $content): View
    {
        $filters = [
            'language' => $request->string('language')->toString() ?: 'en',
            'family' => $request->string('family')->toString() ?: null,
            'search' => $request->string('search')->toString() ?: null,
        ];

        $questions = $content->questions($filters);

        return view('learning.questions', [
            'filters' => $filters,
            'families' => collect($content->sources())->pluck('family')->unique()->sort()->values()->all(),
            'languages' => collect($content->sources())->pluck('language')->filter()->unique()->sort()->values()->all(),
            'questions' => $questions,
        ]);
    }
}
