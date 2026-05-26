<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationIncidentDrillService
{
    /**
     * Build incident practice from the configuration operations runbook.
     */
    public function __construct(
        private readonly ConfigurationOperationsRunbookService $runbook,
    ) {}

    /**
     * Build a realistic configuration incident drill.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $runbook = $this->runbook->build();

        return [
            'title' => 'Configuration Incident Drill',
            'incident_id' => $this->incidentLabel(),
            'summary' => 'Practice diagnosing and recovering from a configuration regression using the accepted decision record and operations runbook.',
            'scenario' => [
                'A small auth configuration change was deployed with passing code review.',
                'The readiness endpoint still loads, but the quality-gate payload no longer uses the expected ready status.',
                'The learner must identify the drift, choose the smallest rollback or patch, and capture recovery evidence.',
            ],
            'runbook' => [
                'runbook_id' => $runbook['runbook_id'],
                'decision_record' => $runbook['decision_record']['record_id'],
                'assessment_score' => $runbook['assessment']['score'],
            ],
            'timeline' => [
                [
                    'minute' => 0,
                    'event' => 'Alert from configuration readiness review.',
                    'action' => 'Open the runbook and confirm the trigger matches a quality-gate status drift.',
                ],
                [
                    'minute' => 5,
                    'event' => 'First diagnostic command finishes.',
                    'action' => 'Compare decision record status and readiness output before changing code.',
                ],
                [
                    'minute' => 15,
                    'event' => 'Root cause is isolated to configuration contract drift.',
                    'action' => 'Patch or rollback the smallest config/env value and clear cached config.',
                ],
                [
                    'minute' => 25,
                    'event' => 'Recovery checks pass.',
                    'action' => 'Attach commands, payloads, and a short incident note to release evidence.',
                ],
            ],
            'diagnosis_steps' => collect($runbook['diagnostics'])
                ->map(fn (array $diagnostic): array => [
                    'step' => $diagnostic['step'],
                    'task' => $diagnostic['check'],
                    'command' => $diagnostic['command'],
                    'success_signal' => $diagnostic['expected'],
                ])
                ->values()
                ->all(),
            'patch_plan' => [
                'Do not change controllers, views, or route definitions until config drift is proven.',
                'Prefer a config/env rollback when the contract was correct before deployment.',
                'If the contract changed intentionally, update readiness checks, tests, decision record, and runbook together.',
                'Re-run the full configuration pipeline checks before marking the incident recovered.',
            ],
            'recovery_evidence' => [
                ...$runbook['evidence'],
                'Incident drill '.$this->incidentLabel().' completed with recovered quality status.',
            ],
            'commands' => [
                ...$runbook['commands'],
                'php artisan test --filter ConfigurationIncidentDrillTest',
            ],
            'handoff' => $runbook['handoff'],
            'assessment' => $runbook['assessment'],
        ];
    }

    /**
     * Return the reusable incident label for evidence strings.
     */
    private function incidentLabel(): string
    {
        return (string) config('labs.configuration.ids.incident');
    }
}
