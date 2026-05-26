<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationInterviewBriefService
{
    /**
     * Create interview briefs from configuration release evidence.
     */
    public function __construct(
        private readonly ConfigurationReleaseEvidenceService $releaseEvidence,
    ) {}

    /**
     * Build interview questions and answer outlines for configuration work.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $evidence = $this->releaseEvidence->build();

        return [
            'title' => 'Configuration Interview Brief',
            'summary' => 'Practice explaining app/auth configuration, deployment safety, rollback thinking, and quality gates with concrete release evidence.',
            'questions' => $this->questions($evidence),
            'evidence_to_cite' => $this->evidenceToCite($evidence),
            'rehearsal_checklist' => [
                'Answer each question with a concrete config key or route.',
                'Reference at least one test command and one smoke endpoint.',
                'Explain the rollback path before discussing deployment confidence.',
                'Keep the quality-gate status tied to observable behavior.',
            ],
            'commands' => [
                ...$evidence['commands'],
                'php artisan test --filter ConfigurationInterviewBriefTest',
            ],
            'quality_gate' => $evidence['quality_gate'],
        ];
    }

    /**
     * Build interview questions with answer outlines.
     *
     * @param  array<string, mixed>  $evidence
     * @return array<int, array{question: string, answer_outline: array<int, string>, follow_up: string}>
     */
    private function questions(array $evidence): array
    {
        return [
            [
                'question' => 'How did you treat Laravel configuration as a runtime contract?',
                'answer_outline' => [
                    'Identify app/auth config files as the contract source.',
                    'Explain that readiness checks read values through config().',
                    'Cite the quality status: '.$evidence['release_summary']['quality_status'].'.',
                ],
                'follow_up' => 'Which config value would you refuse to assert because it changes per environment?',
            ],
            [
                'question' => 'How do you verify an auth configuration change without adding a full login flow?',
                'answer_outline' => [
                    'Assert guard, provider, model, and password broker behavior first.',
                    'Smoke public practice routes to ensure read-only pages remain public.',
                    'Defer protected progress behavior until auth routes exist.',
                ],
                'follow_up' => 'What test would you add before enabling authenticated progress tracking?',
            ],
            [
                'question' => 'What makes the configuration deployment rollback-safe?',
                'answer_outline' => [
                    'Each changed area has a rollback action.',
                    'Config cache is cleared before verification.',
                    'Smoke checks confirm the app returns expected readiness output.',
                ],
                'follow_up' => 'What signal would make you stop the deployment before merging?',
            ],
            [
                'question' => 'Why reuse the quality gate instead of inventing a separate status?',
                'answer_outline' => [
                    'It keeps ready/needs-work language consistent across practice artifacts.',
                    'It centralizes tests, assertion, failure, and Pint readiness checks.',
                    'It makes API consumers depend on one stable readiness shape.',
                ],
                'follow_up' => 'How would you add a new quality criterion without breaking existing consumers?',
            ],
        ];
    }

    /**
     * Build evidence references for interview answers.
     *
     * @param  array<string, mixed>  $evidence
     * @return array<int, array{label: string, source: string}>
     */
    private function evidenceToCite(array $evidence): array
    {
        return [
            [
                'label' => $evidence['release_summary']['headline'],
                'source' => 'Configuration release summary',
            ],
            [
                'label' => $evidence['api_evidence'][0]['endpoint'],
                'source' => $evidence['api_evidence'][0]['expected'],
            ],
            [
                'label' => $evidence['rollback_summary'][1]['area'],
                'source' => $evidence['rollback_summary'][1]['proof'],
            ],
            [
                'label' => 'vendor\\bin\\pint --test',
                'source' => 'Style evidence from verification commands',
            ],
        ];
    }
}
