<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeReleaseReadinessLabTest extends TestCase
{
    /**
     * The release readiness page renders release items and handoff sections.
     */
    public function test_release_readiness_lab_page_renders_release_sections(): void
    {
        $response = $this->get('/practice/release-readiness-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Release readiness lab turns refactor output into a handoff checklist.')
            ->assertSee('Release Rules')
            ->assertSee('Release Items')
            ->assertSee('Handoff Checklist')
            ->assertSee('Release Progress Payload');
    }

    /**
     * The release readiness API returns artifacts generated from refactor tasks.
     */
    public function test_release_readiness_lab_api_returns_release_payload(): void
    {
        $response = $this->getJson('/api/practice/release-readiness-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_refactor_lab.task_count', 3)
            ->assertJsonPath('data.source_refactor_lab.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.release_items.0.changed_area', 'tests/Feature/*Test.php')
            ->assertJsonPath('data.handoff_checklist.0', 'Route or API URL to test after release.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_refactor_lab',
                    'release_rules',
                    'release_items' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'changed_area',
                            'release_note',
                            'smoke_check',
                            'rollback_note',
                            'verification_command',
                            'evidence',
                        ],
                    ],
                    'handoff_checklist',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * JavaScript closure release readiness keeps scope and stale-closure evidence visible.
     */
    public function test_javascript_closure_release_readiness_uses_scope_evidence(): void
    {
        $response = $this->getJson('/api/practice/release-readiness-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_refactor_lab.technology_coverage.0', 'javascript-closures')
            ->assertJsonPath('data.release_items.0.technology_segment', 'Day 1 demo: javascript-closures');

        $this->assertStringContainsString('closure practice flow', $response->json('data.release_items.0.release_note'));
        $this->assertStringContainsString('lexical-scope', $response->json('data.release_items.0.smoke_check'));
        $this->assertStringContainsString('closure-focused verification command', $response->json('data.release_items.0.rollback_note'));
    }

    /**
     * IDOR release readiness keeps object-authorization evidence visible.
     */
    public function test_idor_release_readiness_uses_object_authorization_evidence(): void
    {
        $response = $this->getJson('/api/practice/release-readiness-lab?family=laravel&language=en&search=IDOR&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_refactor_lab.technology_coverage.0', 'idor-access-control')
            ->assertJsonPath('data.release_items.0.technology_segment', 'Day 1 demo: idor-access-control');

        $this->assertStringContainsString('IDOR object-authorization practice flow', $response->json('data.release_items.0.release_note'));
        $this->assertStringContainsString('ID-swap denial', $response->json('data.release_items.0.smoke_check'));
        $this->assertStringContainsString('object-authorization verification command', $response->json('data.release_items.0.rollback_note'));
    }
}
