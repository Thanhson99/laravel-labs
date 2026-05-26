<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticePortfolioLabTest extends TestCase
{
    /**
     * The portfolio lab page renders portfolio entry sections.
     */
    public function test_portfolio_lab_page_renders_entry(): void
    {
        $response = $this->get('/practice/portfolio-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Portfolio lab turns practice work into a reusable learning artifact.')
            ->assertSee('Built a Laravel practice slice for api-validation')
            ->assertSee('Skills Practiced')
            ->assertSee('Portfolio Progress Payload');
    }

    /**
     * The portfolio lab API returns entry, writeup template, and progress payload.
     */
    public function test_portfolio_lab_api_returns_entry_and_template(): void
    {
        $response = $this->getJson('/api/practice/portfolio-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.portfolio_entry.source_reference', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.portfolio_entry.skills_practiced.0', 'API route design')
            ->assertJsonPath('data.writeup_template.0', 'What I built')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'portfolio_entry' => [
                        'headline',
                        'problem',
                        'source_reference',
                        'skills_practiced',
                        'evidence',
                        'next_improvement',
                    ],
                    'writeup_template',
                    'next_labs',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The portfolio lab API returns 404 for unknown records.
     */
    public function test_portfolio_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/portfolio-lab?record_id=missing-record');

        $response->assertNotFound();
    }
}
