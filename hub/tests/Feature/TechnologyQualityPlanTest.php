<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyQualityPlanTest extends TestCase
{
    /**
     * The technology quality plan page renders verification guidance.
     */
    public function test_technology_quality_plan_page_renders_verification_guidance(): void
    {
        $response = $this->get('/practice/technology-quality-plan?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertSee('Quality gates for technology pipelines.')
            ->assertSee('api-validation')
            ->assertSee('php artisan test --filter TechnologyApiValidationTest')
            ->assertSee('vendor\\bin\\pint --test')
            ->assertSee('Open quality API')
            ->assertSee('Open pipeline');
    }

    /**
     * The technology quality plan API returns quality gates and commands.
     */
    public function test_technology_quality_plan_api_returns_quality_gate_payloads(): void
    {
        $response = $this->getJson('/api/practice/technology-quality-plan?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.quality_gate.status', 'ready')
            ->assertJsonPath('data.items.0.quality_gate.passed', true)
            ->assertJsonPath('data.items.0.commands.2', 'vendor\\bin\\pint --test')
            ->assertJsonPath('data.meta.minimum_status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source_count',
                            'pipeline_route',
                            'api_pipeline_route',
                            'quality_gate',
                            'baseline',
                            'commands',
                            'acceptance_checks',
                            'risk_note',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'query',
                        'quality_plan_count',
                        'minimum_status',
                    ],
                ],
            ]);
    }
}
