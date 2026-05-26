<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationPortfolioReviewTest extends TestCase
{
    /**
     * The configuration portfolio review page renders score and rubric.
     */
    public function test_configuration_portfolio_review_page_renders_score_and_rubric(): void
    {
        $response = $this->get('/practice/configuration-portfolio-review');

        $response
            ->assertOk()
            ->assertSee('Configuration Portfolio Review')
            ->assertSee('publish-ready: 100 / 100')
            ->assertSee('Specific configuration claim')
            ->assertSee('Action Items')
            ->assertSee('Open review API');
    }

    /**
     * The configuration portfolio review API returns scored review data.
     */
    public function test_configuration_portfolio_review_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-portfolio-review');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.score', 100)
            ->assertJsonPath('data.result', 'publish-ready')
            ->assertJsonPath('data.rubric.0.criterion', 'Specific configuration claim')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'score',
                    'result',
                    'rubric',
                    'review_notes',
                    'action_items',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
