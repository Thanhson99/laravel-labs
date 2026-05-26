<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationSessionArchiveService
{
    /**
     * Archive the configuration session debrief for future retrieval.
     */
    public function __construct(
        private readonly ConfigurationSessionDebriefService $debrief,
    ) {}

    /**
     * Build the session archive entry for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $debrief = $this->debrief->build();

        return [
            'title' => 'Configuration Session Archive',
            'summary' => 'Store the configuration session debrief as a reusable archive entry for the next practice loop.',
            'archive_id' => $debrief['archive_id'].'-'.config('labs.configuration.session_archive_suffix'),
            'source_archive_id' => $debrief['archive_id'],
            'session_result' => $debrief['session_result'],
            'evidence_tags' => [
                'configuration-session-output',
                'quality-gate-ready',
                'portfolio-rehearsal',
                'incident-recovery-memory',
            ],
            'archive_entries' => collect($debrief['evidence_review'])
                ->map(fn (array $item): array => [
                    'label' => $item['deliverable'],
                    'status' => $item['status'],
                    'note' => $item['review_note'],
                ])
                ->values()
                ->all(),
            'retrieval_prompts' => [
                'Which deliverable proved the selected channel output?',
                'Which source key or route was attached to the session output?',
                'Which blocker should become the next rehearsal prompt?',
                'Which failed command would send the learner back to the risk register?',
            ],
            'reuse_paths' => [
                'Use the captured channel output to revise the portfolio brief.',
                'Use the weakest answer to create the next interview drill.',
                'Use any failed command as input for configuration remediation.',
            ],
            'commands' => [
                ...$debrief['commands'],
                'php artisan test --filter ConfigurationSessionArchiveTest',
            ],
            'quality_gate' => $debrief['quality_gate'],
        ];
    }
}
