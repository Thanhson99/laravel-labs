<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologySpacedReviewTest extends TestCase
{
    /**
     * The spaced review page renders day-based recall and rebuild cards.
     */
    public function test_technology_spaced_review_page_renders_review_cards(): void
    {
        $response = $this->get('/practice/technology-spaced-review/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Spaced Review: api-validation')
            ->assertSee('Day 1 recall')
            ->assertSee('Day 3 rebuild')
            ->assertSee('Day 7 defense')
            ->assertSee('Promotion Criteria')
            ->assertSee('Open review API');
    }

    /**
     * The spaced review API returns review cards, criteria, and progress payload.
     */
    public function test_technology_spaced_review_api_returns_cards_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-spaced-review/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.cards.0.label', 'Day 1 recall')
            ->assertJsonPath('data.cards.1.label', 'Day 3 rebuild')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Day 1 recall')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'checkpoint',
                    'cards' => [
                        '*' => [
                            'day',
                            'label',
                            'recall_prompt',
                            'evidence_recheck',
                            'coding_action',
                        ],
                    ],
                    'promotion_criteria',
                    'progress_payload',
                ],
            ]);
    }
}
