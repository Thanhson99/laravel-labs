<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyMasteryCheckpointTest extends TestCase
{
    /**
     * The mastery checkpoint page renders decision rules, proof, and handoff.
     */
    public function test_technology_mastery_checkpoint_page_renders_decision_and_handoff(): void
    {
        $response = $this->get('/practice/technology-mastery-checkpoint/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Mastery Checkpoint: api-validation')
            ->assertSee('Proof Checklist')
            ->assertSee('Next Challenge')
            ->assertSee('Next Session Handoff')
            ->assertSee('Open checkpoint API');
    }

    /**
     * The mastery checkpoint API returns proof checklist, next challenge, and progress payload.
     */
    public function test_technology_mastery_checkpoint_api_returns_checkpoint_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-mastery-checkpoint/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.next_challenge.route', '/practice/technology-implementation-lab/api-validation')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Confirm repair task evidence')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'remediation_plan',
                    'decision',
                    'proof_checklist',
                    'next_challenge',
                    'handoff',
                    'progress_payload',
                ],
            ]);
    }
}
