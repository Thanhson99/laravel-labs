<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class QuestionDrillSetTest extends TestCase
{
    /**
     * The question drill set page renders JSON questions as coding drill cards.
     */
    public function test_question_drill_set_page_renders_drill_cards(): void
    {
        $response = $this->get('/practice/question-drills?family=laravel&language=en&search=api&technology=api-validation&limit=3');

        $response
            ->assertOk()
            ->assertSee('Each question becomes a small Laravel coding drill.')
            ->assertSee('api-validation')
            ->assertSee('Open coding drill')
            ->assertSee('Open source JSON');
    }

    /**
     * The question drill set API returns record-specific drill links.
     */
    public function test_question_drill_set_api_returns_record_specific_drills(): void
    {
        $response = $this->getJson('/api/practice/question-drills?family=laravel&language=en&search=api&technology=api-validation&limit=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.items.0.drill_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'position',
                            'record_id',
                            'question',
                            'technology',
                            'source',
                            'practice',
                            'drill_query',
                            'task',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'count',
                        'technologies',
                    ],
                ],
            ]);
    }
}
