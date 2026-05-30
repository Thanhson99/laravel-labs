<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationEvidenceArchiveTest extends TestCase
{
    /**
     * The configuration evidence archive page renders proof bundle and retrieval prompts.
     */
    public function test_configuration_evidence_archive_page_renders_archive_artifact(): void
    {
        $response = $this->get('/practice/configuration-evidence-archive');

        $response
            ->assertOk()
            ->assertSee('Configuration Evidence Archive')
            ->assertSee('configuration-contract-promote-100')
            ->assertSee('Proof Bundle')
            ->assertSee('Incident Archive')
            ->assertSee('POSTMORTEM-CONFIG-001')
            ->assertSee('Retrieval Prompts')
            ->assertSee('Open archive API');
    }

    /**
     * The configuration evidence archive API returns retrieval keys and proof bundle.
     */
    public function test_configuration_evidence_archive_api_returns_archive_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-evidence-archive');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.retrieval_keys.0', 'config app auth readiness')
            ->assertJsonPath('data.retrieval_keys.1', 'security misconfiguration release blockers')
            ->assertJsonPath('data.retrieval_keys.2', 'fail closed deployment smoke checks')
            ->assertJsonPath('data.retrieval_keys.6', 'INC-CONFIG-001 POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.incident_archive.postmortem_id', 'POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.proof_bundle.0.label', 'Day 1: Recall the configuration contract.')
            ->assertJsonPath('data.reuse_targets.1', 'Security Misconfiguration note about debug, secrets, CORS, headers, cookies, proxies, storage, and fail-closed release blockers.')
            ->assertJsonPath('data.retrieval_prompts.1', 'Which Security Misconfiguration release blocker would stop deployment even when feature tests pass?')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'retrieval_keys',
                    'incident_archive',
                    'proof_bundle' => [
                        '*' => [
                            'label',
                            'proof',
                        ],
                    ],
                    'reuse_targets',
                    'retrieval_prompts',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
