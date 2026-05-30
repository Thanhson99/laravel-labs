<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationSpacedReviewTest extends TestCase
{
    /**
     * The configuration spaced review page renders day-based review cards.
     */
    public function test_configuration_spaced_review_page_renders_review_cards(): void
    {
        $response = $this->get('/practice/configuration-spaced-review');

        $response
            ->assertOk()
            ->assertSee('Configuration Spaced Review')
            ->assertSee('Day 1')
            ->assertSee('Day 3')
            ->assertSee('Day 7')
            ->assertSee('Incident Memory')
            ->assertSee('POSTMORTEM-CONFIG-001')
            ->assertSee('Promotion Criteria')
            ->assertSee('Open review API');
    }

    /**
     * The configuration spaced review API returns review cards and promotion criteria.
     */
    public function test_configuration_spaced_review_api_returns_schedule_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-spaced-review');

        $response
            ->assertOk()
            ->assertJsonPath('data.decision', 'promote')
            ->assertJsonPath('data.incident_memory.postmortem_id', 'POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.review_cards.0.day', 1)
            ->assertJsonPath('data.review_cards.0.incident_prompt', 'Day 1: replay the incident timeline without looking at the drill page.')
            ->assertJsonPath('data.review_cards.1.defense_prompt', 'Explain what would make you stop a config deployment, including one Security Misconfiguration release blocker.')
            ->assertJsonPath('data.review_cards.2.route', '/practice/configuration-interview-brief')
            ->assertJsonPath('data.promotion_criteria.4', 'Name one Security Misconfiguration release blocker and the smoke check that proves it fails closed.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'decision',
                    'score',
                    'incident_memory',
                    'review_cards' => [
                        '*' => [
                            'day',
                            'focus',
                            'recall_prompt',
                            'rebuild_task',
                            'defense_prompt',
                            'incident_prompt',
                            'route',
                        ],
                    ],
                    'promotion_criteria',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
