<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationLearningPipelineService
{
    /**
     * Create a navigable pipeline for configuration practice.
     */
    public function __construct(
        private readonly ConfigurationEvidenceArchiveService $archive,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build the complete configuration learning pipeline.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $archive = $this->archive->build();
        $stages = $this->stages();

        return [
            'title' => 'Configuration Learning Pipeline',
            'summary' => 'Navigate the full app/auth configuration practice flow from readiness checks through evidence archive.',
            'stage_count' => count($stages),
            'quality_gate' => $archive['quality_gate'],
            'archive' => [
                'archive_id' => $archive['archive_id'],
                'retrieval_keys' => $archive['retrieval_keys'],
                'reuse_targets' => $archive['reuse_targets'],
            ],
            'stages' => $stages,
            'progress_payload' => $this->progressPayload->fromRows(
                $stages,
                fn (array $stage): string => $stage['label'],
            ),
        ];
    }

    /**
     * Return ordered stage definitions for configuration practice.
     *
     * @return array<int, array{step: int, label: string, purpose: string, route: string, api_route: string}>
     */
    private function stages(): array
    {
        return collect([
            ['Readiness', 'Read app/auth config and produce quality-gate status.', 'configuration-readiness'],
            ['Test plan', 'Turn readiness checks into PHPUnit assertions and snippets.', 'configuration-test-plan'],
            ['Change checklist', 'Review impact, before/after checks, rollback, and questions.', 'configuration-change-checklist'],
            ['Risk register', 'Map configuration risks to severity, signals, mitigations, and owner routes.', 'configuration-risk-register'],
            ['Remediation plan', 'Convert configuration risks into file-focused repair tasks and done signals.', 'configuration-remediation-plan'],
            ['PR plan', 'Package remediation work into branch, commit, review, verification, and evidence artifacts.', 'configuration-pull-request-plan'],
            ['Assessment', 'Score remediation PR readiness with a rubric before release evidence reuse.', 'configuration-assessment'],
            ['Decision record', 'Capture the configuration decision, alternatives, consequences, and evidence.', 'configuration-decision-record'],
            ['Operations runbook', 'Translate the accepted decision into diagnostics, rollback, handoff, and evidence steps.', 'configuration-operations-runbook'],
            ['Incident drill', 'Practice diagnosing and recovering from configuration quality-gate drift.', 'configuration-incident-drill'],
            ['Incident postmortem', 'Capture impact, root cause, action items, and spaced-review prompts from the drill.', 'configuration-incident-postmortem'],
            ['Deployment plan', 'Move config changes through preflight, deploy, smoke, and rollback steps.', 'configuration-deployment-plan'],
            ['Release evidence', 'Package deploy proof into a PR-ready release artifact.', 'configuration-release-evidence'],
            ['Interview brief', 'Practice defending the configuration decisions with evidence.', 'configuration-interview-brief'],
            ['Mastery checkpoint', 'Score readiness and decide whether to promote or repeat.', 'configuration-mastery-checkpoint'],
            ['Spaced review', 'Schedule day 1, day 3, and day 7 recall and defense.', 'configuration-spaced-review'],
            ['Evidence archive', 'Store retrieval keys, proof bundle, reuse targets, and prompts.', 'configuration-evidence-archive'],
            ['Archive retrieval', 'Practice retrieving archived configuration proof for reuse.', 'configuration-archive-retrieval'],
            ['Evidence reuse plan', 'Turn retrieved configuration proof into portfolio, interview, review, and incident notes.', 'configuration-evidence-reuse-plan'],
            ['Portfolio brief', 'Package reused configuration evidence into a portfolio and interview artifact.', 'configuration-portfolio-brief'],
            ['Portfolio review', 'Score the configuration portfolio artifact before publishing or rehearsing.', 'configuration-portfolio-review'],
            ['Publication checklist', 'Decide whether the configuration portfolio artifact is ready to publish or rehearse.', 'configuration-publication-checklist'],
            ['Handoff packet', 'Package approved configuration evidence for the next study, interview, or review session.', 'configuration-handoff-packet'],
            ['Next session plan', 'Convert the handoff packet into a timed follow-up practice session.', 'configuration-next-session-plan'],
            ['Session debrief', 'Capture outputs, evidence review, blockers, and next actions after the follow-up session.', 'configuration-session-debrief'],
            ['Session archive', 'Store the follow-up session debrief as a reusable archive entry.', 'configuration-session-archive'],
            ['Archive refresh plan', 'Schedule refresh checks for archived configuration evidence.', 'configuration-archive-refresh-plan'],
            ['Maintenance roadmap', 'Keep configuration evidence current with cadence, owners, health signals, and escalation.', 'configuration-maintenance-roadmap'],
        ])
            ->map(fn (array $stage, int $index): array => [
                'step' => $index + 1,
                'label' => $stage[0],
                'purpose' => $stage[1],
                'route' => '/practice/'.$stage[2],
                'api_route' => '/api/practice/'.$stage[2],
            ])
            ->all();
    }
}
