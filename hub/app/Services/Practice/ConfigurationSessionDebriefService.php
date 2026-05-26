<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationSessionDebriefService
{
    /**
     * Build a debrief from the next-session plan.
     */
    public function __construct(
        private readonly ConfigurationNextSessionPlanService $nextSessionPlan,
    ) {}

    /**
     * Build a post-session debrief for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $plan = $this->nextSessionPlan->build();

        return [
            'title' => 'Configuration Session Debrief',
            'summary' => 'Capture what happened after the configuration next session so evidence, blockers, and follow-up actions are ready for the next loop.',
            'archive_id' => $plan['archive_id'],
            'session_result' => $plan['session_status'] === 'ready' ? 'completed' : 'blocked',
            'completed_outputs' => [
                'One channel-specific answer or paragraph was drafted from the handoff packet.',
                'One proof route or source key was attached to the output.',
                'Focused configuration verification stayed tied to the quality gate.',
            ],
            'evidence_review' => collect($plan['deliverables'])
                ->map(fn (string $deliverable): array => [
                    'deliverable' => $deliverable,
                    'status' => 'captured',
                    'review_note' => 'Keep this deliverable attached to '.$plan['archive_id'].'.',
                ])
                ->values()
                ->all(),
            'blockers' => [
                'If the incident recovery path was not recallable, repeat the postmortem and spaced review cards.',
                'If a claim lacked proof, return to archive retrieval before publishing.',
                'If verification failed, open the remediation plan instead of editing the portfolio artifact.',
            ],
            'next_actions' => [
                'Promote the strongest output into the portfolio brief.',
                'Turn the weakest answer into one new rehearsal prompt.',
                'Carry any failed command back to the configuration risk register.',
            ],
            'commands' => [
                ...$plan['commands'],
                'php artisan test --filter ConfigurationSessionDebriefTest',
            ],
            'quality_gate' => $plan['quality_gate'],
        ];
    }
}
