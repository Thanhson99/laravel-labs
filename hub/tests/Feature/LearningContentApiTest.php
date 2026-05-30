<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LearningContentApiTest extends TestCase
{
    /**
     * The API exposes hub statistics.
     */
    public function test_learning_api_returns_statistics(): void
    {
        $response = $this->getJson('/api/learning');

        $response
            ->assertOk()
            ->assertJsonPath('data.statistics.languages', 2)
            ->assertJsonStructure([
                'data' => [
                    'statistics',
                    'links' => [
                        'sources',
                        'questions',
                    ],
                ],
            ]);
    }

    /**
     * The API returns filtered question-bank data.
     */
    public function test_learning_api_returns_filtered_questions(): void
    {
        $response = $this->getJson('/api/learning/questions?family=laravel&language=en&search=auth');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.family', 'laravel')
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'source_key',
                        'family',
                        'language',
                        'title',
                    ],
                ],
                'meta' => [
                    'filters',
                    'count',
                ],
            ]);
    }

    /**
     * Search tolerates punctuation and matches code, tips, and supporting notes.
     */
    public function test_learning_api_search_normalizes_punctuation_and_code_terms(): void
    {
        $this->getJson('/api/learning/questions?family=laravel&language=en&search=user.normal%20arrow%20function')
            ->assertOk()
            ->assertJsonPath('data.0.source_path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.0.title', '494. Arrow function `this` versus normal function `this` in JavaScript');

        $this->getJson('/api/learning/questions?family=laravel&language=en&search=call-site%20arrow')
            ->assertOk()
            ->assertJsonPath('data.0.source_path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.0.title', '494. Arrow function `this` versus normal function `this` in JavaScript');
    }

    /**
     * The question API defaults to English records when no language is provided.
     */
    public function test_learning_api_defaults_questions_to_english_content(): void
    {
        $response = $this->getJson('/api/learning/questions?family=laravel&search=Sail');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonMissing([
                'title' => 'Sail, môi trường và triển khai: từ local đồng nhất tới production sống khỏe',
            ]);
    }

    /**
     * The question-bank API validates supported language filters.
     */
    public function test_learning_api_validates_question_language(): void
    {
        $response = $this->getJson('/api/learning/questions?language=fr');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('language');
    }

    /**
     * The API returns one decoded source by key.
     */
    public function test_learning_api_returns_one_source(): void
    {
        $response = $this->getJson('/api/learning/sources/interview-senior-en-json');

        $response
            ->assertOk()
            ->assertJsonPath('data.path', 'interview/senior.en.json')
            ->assertJsonPath('data.family', 'interview');
    }

    /**
     * The source index API defaults to English sources for hub browsing.
     */
    public function test_learning_api_source_index_defaults_to_english_sources(): void
    {
        $response = $this->getJson('/api/learning/sources');

        $response
            ->assertOk()
            ->assertJsonMissing([
                'language' => 'vi',
            ])
            ->assertJsonMissing([
                'title' => 'Sail, môi trường và triển khai: từ local đồng nhất tới production sống khỏe',
            ]);
    }

    /**
     * The API returns a generated quiz practice set.
     */
    public function test_learning_api_returns_quiz_practice_set(): void
    {
        $response = $this->getJson('/api/learning/quiz?family=laravel&language=en&search=auth&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.family', 'laravel')
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonPath('meta.filters.limit', 3)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'position',
                        'id',
                        'prompt',
                        'context',
                        'answer',
                        'code',
                        'source' => [
                            'key',
                            'path',
                            'family',
                            'topic',
                            'language',
                        ],
                    ],
                ],
                'meta' => [
                    'filters',
                    'available',
                    'count',
                ],
            ]);
    }

    /**
     * The quiz API defaults to English records when no language is provided.
     */
    public function test_learning_api_defaults_quiz_to_english_content(): void
    {
        $response = $this->getJson('/api/learning/quiz?family=laravel&search=Sail&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonMissing([
                'prompt' => 'Sail, môi trường và triển khai: từ local đồng nhất tới production sống khỏe',
            ]);
    }

    /**
     * The API validates quiz limits before generating a practice set.
     */
    public function test_learning_api_validates_quiz_limit(): void
    {
        $response = $this->getJson('/api/learning/quiz?limit=100');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('limit');
    }

    /**
     * The API returns generated source/topic study-plan modules.
     */
    public function test_learning_api_returns_study_plan(): void
    {
        $response = $this->getJson('/api/learning/study-plan?family=laravel&language=en&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.family', 'laravel')
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonPath('meta.filters.limit', 3)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'step',
                        'source_key',
                        'source_path',
                        'family',
                        'topic',
                        'language',
                        'title',
                        'item_count',
                        'estimated_minutes',
                        'outcomes',
                    ],
                ],
                'meta' => [
                    'filters',
                    'available_sources',
                    'count',
                    'estimated_minutes',
                ],
            ]);
    }

    /**
     * The study-plan API defaults to English sources when no language is provided.
     */
    public function test_learning_api_defaults_study_plan_to_english_content(): void
    {
        $response = $this->getJson('/api/learning/study-plan?family=laravel&search=Sail&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonMissing([
                'title' => 'Sail, môi trường và triển khai: từ local đồng nhất tới production sống khỏe',
            ]);
    }

    /**
     * The API validates study-plan limits before generating modules.
     */
    public function test_learning_api_validates_study_plan_limit(): void
    {
        $response = $this->getJson('/api/learning/study-plan?limit=50');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('limit');
    }

    /**
     * The API returns content analytics.
     */
    public function test_learning_api_returns_analytics(): void
    {
        $response = $this->getJson('/api/learning/analytics');

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.languages', 2)
            ->assertJsonStructure([
                'data' => [
                    'summary',
                    'families' => [
                        '*' => [
                            'key',
                            'count',
                        ],
                    ],
                    'languages',
                    'types',
                    'source_density',
                    'code_density',
                ],
            ])
            ->assertJsonMissing([
                'language' => 'vi',
            ]);
    }

    /**
     * The API returns hands-on labs generated from content topics.
     */
    public function test_learning_api_returns_labs(): void
    {
        $response = $this->getJson('/api/learning/labs?family=laravel&language=en&track=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.family', 'laravel')
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonPath('meta.filters.track', 'api')
            ->assertJsonPath('meta.filters.limit', 3)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'number',
                        'id',
                        'track',
                        'title',
                        'source',
                        'goal',
                        'tasks',
                        'files',
                        'commands',
                        'done',
                    ],
                ],
                'meta' => [
                    'filters',
                    'available_records',
                    'count',
                    'tracks',
                ],
            ]);
    }

    /**
     * The labs API defaults to English content when no language filter is provided.
     */
    public function test_learning_api_defaults_labs_to_english_content(): void
    {
        $response = $this->getJson('/api/learning/labs?family=laravel&track=docker-devops&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.language', 'en')
            ->assertJsonPath('data.0.source.language', 'en')
            ->assertJsonMissing([
                'title' => 'Docker DevOps lab: Sail, môi trường và triển khai: từ local đồng nhất tới production sống khỏe',
            ]);
    }

    /**
     * The API validates lab track names.
     */
    public function test_learning_api_validates_lab_track(): void
    {
        $response = $this->getJson('/api/learning/labs?track=unknown');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('track');
    }
}
