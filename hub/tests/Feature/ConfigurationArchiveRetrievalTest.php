<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationArchiveRetrievalTest extends TestCase
{
    /**
     * The configuration archive retrieval page renders retrieval cases.
     */
    public function test_configuration_archive_retrieval_page_renders_cases(): void
    {
        $response = $this->get('/practice/configuration-archive-retrieval');

        $response
            ->assertOk()
            ->assertSee('Configuration Archive Retrieval')
            ->assertSee('Retrieval Cases')
            ->assertSee('Incident recovery')
            ->assertSee('Quality Checks')
            ->assertSee('Open retrieval API');
    }

    /**
     * The configuration archive retrieval API returns cases and quality checks.
     */
    public function test_configuration_archive_retrieval_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-archive-retrieval');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.retrieval_cases.0.use_case', 'Portfolio note')
            ->assertJsonPath('data.retrieval_cases.2.key', 'INC-CONFIG-001 POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'retrieval_cases',
                    'search_keys',
                    'reuse_targets',
                    'quality_checks',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
