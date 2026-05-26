<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyEvidenceArchiveTest extends TestCase
{
    /**
     * The evidence archive page renders retrieval keys, proof, and reuse targets.
     */
    public function test_technology_evidence_archive_page_renders_archive_artifacts(): void
    {
        $response = $this->get('/practice/technology-evidence-archive/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Evidence Archive: api-validation')
            ->assertSee('Retrieval Keys')
            ->assertSee('Proof Bundle')
            ->assertSee('Reuse Targets')
            ->assertSee('technology:api-validation')
            ->assertSee('Open archive API');
    }

    /**
     * The evidence archive API returns archive id, retrieval keys, proof, and progress payload.
     */
    public function test_technology_evidence_archive_api_returns_archive_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-evidence-archive/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.retrieval_keys.0', 'technology:api-validation')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Save archive id')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'archive_id',
                    'review',
                    'retrieval_keys',
                    'proof_bundle' => [
                        '*' => [
                            'label',
                            'detail',
                        ],
                    ],
                    'reuse_targets',
                    'retrieval_prompts',
                    'progress_payload',
                ],
            ]);
    }
}
