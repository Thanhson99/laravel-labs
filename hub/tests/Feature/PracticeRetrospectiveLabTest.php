<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeRetrospectiveLabTest extends TestCase
{
    /**
     * The retrospective lab page renders wins and next actions.
     */
    public function test_retrospective_lab_page_renders_next_actions(): void
    {
        $response = $this->get('/practice/retrospective-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Retrospective lab turns assessment scores into next learning actions.')
            ->assertSee('Weak Spots And Next Actions')
            ->assertSee('Re-run assessment lab')
            ->assertSee('Retrospective Progress Payload');
    }

    /**
     * The retrospective lab API returns wins, weak spots, next labs, and progress payload.
     */
    public function test_retrospective_lab_api_returns_next_actions(): void
    {
        $response = $this->getJson('/api/practice/retrospective-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.score_total', 100)
            ->assertJsonPath('data.weak_spots.0.label', 'Content traceability')
            ->assertJsonPath('data.next_labs.1.route', 'practice.assessment-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'score_total',
                    'wins',
                    'weak_spots' => [
                        '*' => [
                            'label',
                            'prompt',
                            'next_action',
                        ],
                    ],
                    'next_labs',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The retrospective lab API returns 404 for unknown records.
     */
    public function test_retrospective_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/retrospective-lab?record_id=missing-record');

        $response->assertNotFound();
    }
}
