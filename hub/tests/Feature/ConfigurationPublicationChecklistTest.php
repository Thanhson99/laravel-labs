<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationPublicationChecklistTest extends TestCase
{
    /**
     * The configuration publication checklist page renders publication controls.
     */
    public function test_configuration_publication_checklist_page_renders_controls(): void
    {
        $response = $this->get('/practice/configuration-publication-checklist');

        $response
            ->assertOk()
            ->assertSee('Configuration Publication Checklist')
            ->assertSee('approved: 100 / 100')
            ->assertSee('Pre-Publish Checks')
            ->assertSee('Do Not Publish If')
            ->assertSee('Open checklist API');
    }

    /**
     * The configuration publication checklist API returns status and channels.
     */
    public function test_configuration_publication_checklist_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-publication-checklist');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.publication_status', 'approved')
            ->assertJsonPath('data.channels.0.name', 'Portfolio')
            ->assertJsonPath('data.channels.1.name', 'Security review')
            ->assertJsonPath('data.channels.1.proof_required', 'Unsafe signal, release blocker, owner, rollback action, and fail-closed smoke check.')
            ->assertJsonPath('data.pre_publish_checks.2', 'Security Misconfiguration claims name release blockers and fail-closed smoke evidence.')
            ->assertJsonPath('data.do_not_publish_if.1', 'Security Misconfiguration is described without an unsafe signal, owner, rollback action, or smoke check.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'publication_status',
                    'score',
                    'channels',
                    'pre_publish_checks',
                    'do_not_publish_if',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
