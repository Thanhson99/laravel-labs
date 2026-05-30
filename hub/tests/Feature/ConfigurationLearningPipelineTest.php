<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationLearningPipelineTest extends TestCase
{
    /**
     * The configuration learning pipeline page renders every configuration stage.
     */
    public function test_configuration_learning_pipeline_page_renders_full_stage_flow(): void
    {
        $response = $this->get('/practice/configuration-learning-pipeline');

        $response
            ->assertOk()
            ->assertSee('Configuration Learning Pipeline')
            ->assertSee('Readiness')
            ->assertSee('Assessment')
            ->assertSee('Decision record')
            ->assertSee('Operations runbook')
            ->assertSee('Incident drill')
            ->assertSee('Incident postmortem')
            ->assertSee('Deployment plan')
            ->assertSee('Evidence archive')
            ->assertSee('Archive retrieval')
            ->assertSee('Evidence reuse plan')
            ->assertSee('Portfolio brief')
            ->assertSee('Portfolio review')
            ->assertSee('Publication checklist')
            ->assertSee('Handoff packet')
            ->assertSee('Next session plan')
            ->assertSee('Session debrief')
            ->assertSee('Session archive')
            ->assertSee('Archive refresh plan')
            ->assertSee('Maintenance roadmap')
            ->assertSee('configuration-contract-promote-100')
            ->assertSee('Reuse targets')
            ->assertSee('Security Misconfiguration note about debug, secrets, CORS, headers, cookies, proxies, storage, and fail-closed release blockers.')
            ->assertSee('Open pipeline API');
    }

    /**
     * The configuration learning pipeline API returns stages, archive, and progress payload.
     */
    public function test_configuration_learning_pipeline_api_returns_stages_and_progress(): void
    {
        $response = $this->getJson('/api/practice/configuration-learning-pipeline');

        $response
            ->assertOk()
            ->assertJsonPath('data.stage_count', 28)
            ->assertJsonPath('data.stages.0.label', 'Readiness')
            ->assertJsonPath('data.stages.6.label', 'Assessment')
            ->assertJsonPath('data.stages.7.label', 'Decision record')
            ->assertJsonPath('data.stages.8.label', 'Operations runbook')
            ->assertJsonPath('data.stages.9.label', 'Incident drill')
            ->assertJsonPath('data.stages.10.label', 'Incident postmortem')
            ->assertJsonPath('data.stages.16.label', 'Evidence archive')
            ->assertJsonPath('data.stages.17.label', 'Archive retrieval')
            ->assertJsonPath('data.stages.18.label', 'Evidence reuse plan')
            ->assertJsonPath('data.stages.19.label', 'Portfolio brief')
            ->assertJsonPath('data.stages.20.label', 'Portfolio review')
            ->assertJsonPath('data.stages.21.label', 'Publication checklist')
            ->assertJsonPath('data.stages.22.label', 'Handoff packet')
            ->assertJsonPath('data.stages.23.label', 'Next session plan')
            ->assertJsonPath('data.stages.24.label', 'Session debrief')
            ->assertJsonPath('data.stages.25.label', 'Session archive')
            ->assertJsonPath('data.stages.26.label', 'Archive refresh plan')
            ->assertJsonPath('data.stages.27.label', 'Maintenance roadmap')
            ->assertJsonPath('data.archive.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.archive.retrieval_keys.1', 'security misconfiguration release blockers')
            ->assertJsonPath('data.archive.reuse_targets.1', 'Security Misconfiguration note about debug, secrets, CORS, headers, cookies, proxies, storage, and fail-closed release blockers.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'stage_count',
                    'quality_gate',
                    'archive',
                    'stages' => [
                        '*' => [
                            'step',
                            'label',
                            'purpose',
                            'route',
                            'api_route',
                        ],
                    ],
                    'progress_payload',
                ],
            ]);
    }
}
