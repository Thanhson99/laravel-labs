<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationSpacedReviewService
{
    /**
     * Create spaced review schedules from configuration mastery checkpoints.
     */
    public function __construct(
        private readonly ConfigurationMasteryCheckpointService $checkpoint,
    ) {}

    /**
     * Build a spaced review schedule for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $checkpoint = $this->checkpoint->build();

        return [
            'title' => 'Configuration Spaced Review',
            'summary' => 'Review app/auth configuration practice on day 1, day 3, and day 7 so the runtime contract, incident recovery path, and postmortem lessons stay recallable and defensible.',
            'decision' => $checkpoint['decision'],
            'score' => $checkpoint['score'],
            'incident_memory' => $this->incidentMemory(),
            'review_cards' => $this->reviewCards($checkpoint),
            'promotion_criteria' => [
                'Explain config() versus env() without looking at notes.',
                'Name one app config value and one auth config value that are tested.',
                'Describe rollback for an auth provider or password throttle change.',
                'Replay the incident root cause and recovery path from the postmortem.',
                'Name one Security Misconfiguration release blocker and the smoke check that proves it fails closed.',
                'Run the focused configuration tests and Pint without failures.',
            ],
            'commands' => [
                ...$checkpoint['commands'],
                'php artisan test --filter ConfigurationIncidentPostmortemTest',
                'php artisan test --filter ConfigurationSpacedReviewTest',
            ],
            'quality_gate' => $checkpoint['quality_gate'],
        ];
    }

    /**
     * Return review cards for day 1, day 3, and day 7.
     *
     * @param  array<string, mixed>  $checkpoint
     * @return array<int, array<string, mixed>>
     */
    private function reviewCards(array $checkpoint): array
    {
        $incidentPrompts = $this->incidentMemory()['review_inputs'];

        return [
            [
                'day' => 1,
                'focus' => 'Recall the configuration contract.',
                'recall_prompt' => 'List the app and auth config keys protected by the readiness checks.',
                'rebuild_task' => 'Open the readiness route and restate why each check exists.',
                'defense_prompt' => 'Explain why the checkpoint decision is '.$checkpoint['decision'].' with '.$checkpoint['score'].' points.',
                'incident_prompt' => $incidentPrompts[0],
                'route' => $checkpoint['handoff']['next_route'],
            ],
            [
                'day' => 3,
                'focus' => 'Rebuild deployment safety.',
                'recall_prompt' => 'Describe the deploy, smoke, and rollback order from memory.',
                'rebuild_task' => 'Recreate the rollback explanation for app runtime and auth contract changes.',
                'defense_prompt' => 'Explain what would make you stop a config deployment, including one Security Misconfiguration release blocker.',
                'incident_prompt' => $incidentPrompts[1],
                'route' => $checkpoint['handoff']['evidence_route'],
            ],
            [
                'day' => 7,
                'focus' => 'Defend the design.',
                'recall_prompt' => 'Answer one interview question using a config key, route, and command.',
                'rebuild_task' => 'Turn the interview brief into a two-minute spoken explanation.',
                'defense_prompt' => 'Explain why the shared quality gate is better than a one-off status.',
                'incident_prompt' => $incidentPrompts[2],
                'route' => $checkpoint['handoff']['interview_route'],
            ],
        ];
    }

    /**
     * Return postmortem references without creating a service dependency cycle.
     *
     * @return array<string, mixed>
     */
    private function incidentMemory(): array
    {
        return [
            'postmortem_id' => (string) config('labs.configuration.ids.postmortem'),
            'incident_id' => (string) config('labs.configuration.ids.incident'),
            'root_cause' => (string) config('labs.configuration.incident_root_cause'),
            'recovery_route' => '/practice/configuration-incident-postmortem',
            'review_inputs' => [
                'Day 1: replay the incident timeline without looking at the drill page.',
                'Day 3: explain the root cause and smallest rollback path.',
                'Day 7: defend why the shared quality-gate shape prevents repeated ambiguity.',
            ],
        ];
    }
}
