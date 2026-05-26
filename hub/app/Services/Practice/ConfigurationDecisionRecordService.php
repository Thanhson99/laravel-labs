<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationDecisionRecordService
{
    /**
     * Turn configuration assessment output into an ADR-style learning record.
     */
    public function __construct(
        private readonly ConfigurationAssessmentService $assessment,
    ) {}

    /**
     * Build a decision record for app, auth, and quality-gate configuration.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $assessment = $this->assessment->build();

        return [
            'title' => 'Configuration Decision Record',
            'record_id' => (string) config('labs.configuration.ids.decision_record'),
            'status' => $assessment['result'] === 'ready' ? 'accepted' : 'proposed',
            'summary' => 'Capture why the hub keeps app/auth configuration and practice quality gates as explicit, tested contracts before deployment.',
            'context' => [
                'Configuration changes affect runtime identity, authentication defaults, and learner-facing quality reports.',
                'The practice hub uses read-only content plus native Laravel exercises, so config changes must remain easy to review and test.',
                'Assessment score is '.$assessment['score'].'/100 with quality status '.$assessment['status']['quality'].'.',
            ],
            'decision' => [
                'Keep app/auth configuration changes small and route them through readiness, tests, risk review, remediation, PR, and assessment before deployment.',
                'Reuse the shared practice quality-gate status shape instead of creating feature-specific pass/fail contracts.',
                'Document verification commands and evidence with each configuration change so release evidence and interview briefs stay defensible.',
            ],
            'alternatives' => [
                [
                    'option' => 'Controller-owned configuration checks',
                    'tradeoff' => 'Faster to write but mixes routing, orchestration, and business rules, making learner examples harder to scan.',
                ],
                [
                    'option' => 'Manual checklist without API payloads',
                    'tradeoff' => 'Readable in a browser but weak for tests, automation, and evidence reuse.',
                ],
                [
                    'option' => 'Separate quality status per configuration page',
                    'tradeoff' => 'Looks flexible but creates inconsistent status semantics across the practice workspace.',
                ],
            ],
            'consequences' => [
                'New configuration features should expose both a Blade page and API payload.',
                'Tests must cover page rendering, JSON structure, route comments, and shared quality status.',
                'Future deployment or release features can cite this record instead of repeating the rationale.',
            ],
            'evidence' => [
                ...$assessment['evidence'],
                'Assessment rubric reached '.$assessment['result_label'].'.',
            ],
            'commands' => [
                ...$assessment['commands'],
                'php artisan test --filter ConfigurationDecisionRecordTest',
            ],
            'assessment' => [
                'score' => $assessment['score'],
                'result' => $assessment['result'],
                'quality' => $assessment['status']['quality'],
            ],
        ];
    }
}
