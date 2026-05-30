<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationHandoffPacketTest extends TestCase
{
    /**
     * The configuration handoff packet page renders handoff sections.
     */
    public function test_configuration_handoff_packet_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-handoff-packet');

        $response
            ->assertOk()
            ->assertSee('Configuration Handoff Packet')
            ->assertSee('ready-to-handoff')
            ->assertSee('Required Evidence')
            ->assertSee('Next Actions')
            ->assertSee('Open handoff API');
    }

    /**
     * The configuration handoff packet API returns links and evidence.
     */
    public function test_configuration_handoff_packet_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-handoff-packet');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.handoff_status', 'ready-to-handoff')
            ->assertJsonPath('data.packet_links.0.label', 'Publication checklist')
            ->assertJsonPath('data.required_evidence.0.channel', 'Portfolio')
            ->assertJsonPath('data.required_evidence.1.channel', 'Security review')
            ->assertJsonPath('data.rehearsal_prompts.1', 'Explain which Security Misconfiguration release blocker would stop deployment and which smoke check proves it.')
            ->assertJsonPath('data.next_actions.1', 'Open the Security review channel and attach one fail-closed smoke-check proof.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'handoff_status',
                    'handoff_summary',
                    'packet_links',
                    'required_evidence',
                    'rehearsal_prompts',
                    'next_actions',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
