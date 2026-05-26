<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LearningContentRepositoryInterface;
use App\Services\Learning\LearningQuizService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class QuizController extends Controller
{
    /**
     * Show a randomized practice set built from JSON-backed learning content.
     */
    public function __invoke(
        Request $request,
        LearningQuizService $quiz,
        LearningContentRepositoryInterface $content,
    ): View {
        $filters = [
            'language' => $request->string('language')->toString() ?: 'en',
            'family' => $request->string('family')->toString() ?: null,
            'search' => $request->string('search')->toString() ?: null,
            'limit' => $request->integer('limit') ?: null,
        ];

        return view('learning.quiz', [
            'filters' => $filters,
            'families' => collect($content->sources())->pluck('family')->unique()->sort()->values()->all(),
            'languages' => collect($content->sources())->pluck('language')->filter()->unique()->sort()->values()->all(),
            'quiz' => $quiz->build($filters),
        ]);
    }
}
