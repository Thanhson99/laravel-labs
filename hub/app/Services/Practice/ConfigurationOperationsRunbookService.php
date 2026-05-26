<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationOperationsRunbookService
{
    /**
     * Build operational guidance from the accepted configuration decision.
     */
    public function __construct(
        private readonly ConfigurationDecisionRecordService $decisionRecord,
    ) {}

    /**
     * Build an operations runbook for configuration incidents and releases.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $decisionRecord = $this->decisionRecord->build();

        return [
            'title' => 'Configuration Operations Runbook',
            'runbook_id' => (string) config('labs.configuration.ids.runbook'),
            'summary' => 'Translate configuration decisions into repeatable operational checks for runtime failures, deploy verification, rollback, and evidence capture.',
            'decision_record' => [
                'record_id' => $decisionRecord['record_id'],
                'status' => $decisionRecord['status'],
                'assessment_score' => $decisionRecord['assessment']['score'],
            ],
            'triggers' => [
                'Configuration readiness API stops returning ready.',
                'Authentication defaults change unexpectedly after a deploy.',
                'Practice quality-gate payloads no longer share the same status shape.',
                'Route or Pint verification fails during configuration review.',
            ],
            'diagnostics' => [
                [
                    'step' => 1,
                    'check' => 'Confirm the current decision record is accepted.',
                    'command' => 'php artisan test --filter ConfigurationDecisionRecordTest',
                    'expected' => 'The decision record API reports status accepted.',
                ],
                [
                    'step' => 2,
                    'check' => 'Verify app/auth readiness and quality-gate status.',
                    'command' => 'php artisan test --filter ConfigurationReadinessTest',
                    'expected' => 'Readiness checks return ready and include app/auth source files.',
                ],
                [
                    'step' => 3,
                    'check' => 'Confirm learner-facing configuration routes are documented.',
                    'command' => 'php artisan test --filter RouteFileDocumentationTest',
                    'expected' => 'Every configuration route still has a learner-facing purpose comment.',
                ],
            ],
            'rollback' => [
                'Revert the smallest config/env change that caused the readiness or auth drift.',
                'Run config clear before checking runtime values again.',
                'Re-run readiness, decision record, route documentation, and Pint checks.',
                'Attach the failed and recovered payloads to the PR or release evidence.',
            ],
            'handoff' => [
                'Incident summary names the changed config key and affected route.',
                'Decision record link is included so reviewers understand the accepted contract.',
                'Commands and API payloads are copied into release evidence.',
                'Any repeated failure becomes a remediation task instead of a one-off note.',
            ],
            'evidence' => [
                ...$decisionRecord['evidence'],
                'Operations runbook cites '.$decisionRecord['record_id'].' with status '.$decisionRecord['status'].'.',
            ],
            'commands' => [
                ...$decisionRecord['commands'],
                'php artisan test --filter ConfigurationOperationsRunbookTest',
            ],
            'assessment' => $decisionRecord['assessment'],
        ];
    }
}
