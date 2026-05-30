<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationArchiveRefreshPlanService
{
    /**
     * Build refresh guidance from the configuration session archive.
     */
    public function __construct(
        private readonly ConfigurationSessionArchiveService $sessionArchive,
    ) {}

    /**
     * Build a refresh plan for archived configuration evidence.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $archive = $this->sessionArchive->build();

        return [
            'title' => 'Configuration Archive Refresh Plan',
            'summary' => 'Keep archived configuration evidence current by scheduling refresh checks, rerun commands, and remediation triggers.',
            'archive_id' => $archive['archive_id'],
            'refresh_status' => $archive['session_result'] === 'completed' ? 'scheduled' : 'blocked',
            'refresh_triggers' => [
                'A configuration route, config file, or quality-gate payload changes.',
                'A Security Misconfiguration control, release blocker, or smoke check changes.',
                'A portfolio or interview answer cites stale proof.',
                'A session archive blocker repeats twice.',
                'Any focused verification command fails after a config change.',
            ],
            'refresh_tasks' => collect($archive['archive_entries'])
                ->map(fn (array $entry): array => [
                    'evidence' => $entry['label'],
                    'current_status' => $entry['status'],
                    'refresh_action' => 'Re-run the source command or route and update the archive note.',
                ])
                ->values()
                ->all(),
            'rerun_commands' => [
                'php artisan test --filter ConfigurationSessionArchiveTest',
                'php artisan test --filter ConfigurationEvidenceArchiveTest',
                'php artisan test --filter ConfigurationLearningPipelineTest',
                'vendor\\bin\\pint --test',
            ],
            'remediation_triggers' => [
                'Refresh action cannot reproduce the original proof.',
                'Quality-gate status changes away from ready.',
                'Security Misconfiguration release blockers no longer map to an owner, rollback action, and smoke check.',
                'Archive retrieval can no longer find the postmortem or incident source key.',
            ],
            'commands' => [
                ...$archive['commands'],
                'php artisan test --filter ConfigurationArchiveRefreshPlanTest',
            ],
            'quality_gate' => $archive['quality_gate'],
        ];
    }
}
