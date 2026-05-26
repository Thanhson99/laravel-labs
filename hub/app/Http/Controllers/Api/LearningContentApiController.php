<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FilterQuestionsRequest;
use App\Http\Requests\Api\GenerateLabsRequest;
use App\Http\Requests\Api\GenerateQuizRequest;
use App\Http\Requests\Api\GenerateStudyPlanRequest;
use App\Repositories\Contracts\LearningContentRepositoryInterface;
use App\Services\Learning\LearningAnalyticsService;
use App\Services\Learning\LearningLabService;
use App\Services\Learning\LearningQuizService;
use App\Services\Learning\LearningStudyPlanService;
use Illuminate\Http\JsonResponse;

final class LearningContentApiController extends Controller
{
    /**
     * Return hub statistics and API affordances.
     */
    public function index(LearningContentRepositoryInterface $content): JsonResponse
    {
        return $this->jsonData([
            'statistics' => $content->statistics(),
            'links' => [
                'sources' => route('api.learning.sources'),
                'questions' => route('api.learning.questions'),
                'quiz' => route('api.learning.quiz'),
                'study_plan' => route('api.learning.study-plan'),
                'analytics' => route('api.learning.analytics'),
                'labs' => route('api.learning.labs'),
            ],
        ]);
    }

    /**
     * Return all discovered JSON sources.
     */
    public function sources(LearningContentRepositoryInterface $content): JsonResponse
    {
        return $this->jsonData(collect($content->sources())
            ->where('language', 'en')
            ->map(fn (array $source): array => collect($source)->except('payload')->all())
            ->values()
            ->all());
    }

    /**
     * Return filtered question-bank records.
     */
    public function questions(FilterQuestionsRequest $request, LearningContentRepositoryInterface $content): JsonResponse
    {
        $filters = $request->filters();

        $questions = $content->questions($filters);

        return $this->jsonDataWithMeta($questions, [
            'filters' => $filters,
            'count' => count($questions),
        ]);
    }

    /**
     * Return a generated practice set from filtered question-bank records.
     */
    public function quiz(GenerateQuizRequest $request, LearningQuizService $quiz): JsonResponse
    {
        $practiceSet = $quiz->build($request->filters());

        return $this->jsonDataWithMeta($practiceSet['items'], $practiceSet['meta']);
    }

    /**
     * Return a generated study plan from filtered source modules.
     */
    public function studyPlan(GenerateStudyPlanRequest $request, LearningStudyPlanService $studyPlan): JsonResponse
    {
        $plan = $studyPlan->build($request->filters());

        return $this->jsonDataWithMeta($plan['modules'], $plan['meta']);
    }

    /**
     * Return analytics for the integrated JSON content.
     */
    public function analytics(LearningAnalyticsService $analytics): JsonResponse
    {
        return $this->jsonData($analytics->report());
    }

    /**
     * Return generated hands-on labs from filtered learning content.
     */
    public function labs(GenerateLabsRequest $request, LearningLabService $labs): JsonResponse
    {
        $result = $labs->build($request->filters());

        return $this->jsonDataWithMeta($result['labs'], $result['meta']);
    }

    /**
     * Return one decoded JSON source by source key.
     */
    public function source(string $sourceKey, LearningContentRepositoryInterface $content): JsonResponse
    {
        $source = $content->findSource($sourceKey);

        return $this->jsonDataOrNotFound($source, 'Learning source not found.');
    }
}
