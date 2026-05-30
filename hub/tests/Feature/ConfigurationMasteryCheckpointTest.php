<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationMasteryCheckpointTest extends TestCase
{
    /**
     * The configuration mastery checkpoint page renders scorecard and next actions.
     */
    public function test_configuration_mastery_checkpoint_page_renders_scorecard(): void
    {
        $response = $this->get('/practice/configuration-mastery-checkpoint');

        $response
            ->assertOk()
            ->assertSee('Configuration Mastery Checkpoint')
            ->assertSee('Runtime contract is testable.')
            ->assertSee('Repeat Triggers')
            ->assertSee('Next Actions')
            ->assertSee('Open checkpoint API');
    }

    /**
     * The configuration mastery checkpoint API returns decision, scorecard, and handoff routes.
     */
    public function test_configuration_mastery_checkpoint_api_returns_decision_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-mastery-checkpoint');

        $response
            ->assertOk()
            ->assertJsonPath('data.decision', 'promote')
            ->assertJsonPath('data.score', 100)
            ->assertJsonPath('data.scorecard.0.criterion', 'Runtime contract is testable.')
            ->assertJsonPath('data.scorecard.4.criterion', 'Security Misconfiguration is release-blocked.')
            ->assertJsonPath('data.scorecard.4.points', 0)
            ->assertJsonPath('data.repeat_triggers.4', 'Security Misconfiguration is described as a checklist only, without fail-closed smoke checks or release blockers.')
            ->assertJsonPath('data.next_actions.2', 'Reuse the Security Misconfiguration release-blocker evidence in interview and deployment notes.')
            ->assertJsonPath('data.handoff.interview_route', '/practice/configuration-interview-brief')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'score',
                    'decision',
                    'scorecard',
                    'repeat_triggers',
                    'next_actions',
                    'handoff',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
