<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentPracticeSyllabusTest extends TestCase
{
    /**
     * The syllabus page renders technology phases and source packs.
     */
    public function test_syllabus_page_renders_phases_and_source_packs(): void
    {
        $response = $this->get('/practice/syllabus?family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertSee('A practice syllabus generated from source content and technologies.')
            ->assertSee('api-validation')
            ->assertSee('Open technology board')
            ->assertSee('Source Packs');
    }

    /**
     * The syllabus API returns phases, queue queries, and source packs.
     */
    public function test_syllabus_api_returns_phases_and_source_packs(): void
    {
        $response = $this->getJson('/api/practice/syllabus?family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('data.phases.0.technology', 'api-validation')
            ->assertJsonPath('data.phases.0.queue_query.technology', 'api-validation')
            ->assertJsonPath('data.source_packs.0.source.path', 'laravel/api-integration.en.json')
            ->assertJsonStructure([
                'data' => [
                    'phases' => [
                        '*' => [
                            'phase',
                            'technology',
                            'title',
                            'record_count',
                            'source_count',
                            'exercise',
                            'sample_drill_query',
                            'queue_query',
                            'done_when',
                        ],
                    ],
                    'source_packs',
                    'meta',
                ],
            ]);
    }

    /**
     * The syllabus can focus on the XSS defense learning path.
     */
    public function test_syllabus_api_returns_xss_defense_phase(): void
    {
        $this->getJson('/api/practice/syllabus?family=laravel&language=en&search=XSS&limit=5')
            ->assertOk()
            ->assertJsonPath('data.phases.0.technology', 'xss-defense')
            ->assertJsonPath('data.phases.0.exercise.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.phases.0.queue_query.technology', 'xss-defense')
            ->assertJsonPath('data.source_packs.0.source.path', 'laravel/auth-security.en.json');
    }
}
