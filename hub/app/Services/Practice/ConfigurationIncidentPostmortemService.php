<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationIncidentPostmortemService
{
    /**
     * Build a learning postmortem from the configuration incident drill.
     */
    public function __construct(
        private readonly ConfigurationIncidentDrillService $incidentDrill,
    ) {}

    /**
     * Build the postmortem artifact for one configuration incident drill.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $drill = $this->incidentDrill->build();

        return [
            'title' => 'Configuration Incident Postmortem',
            'postmortem_id' => $this->postmortemLabel(),
            'summary' => 'Turn the configuration incident drill into a blameless learning record with impact, root cause, action items, and review prompts.',
            'incident' => [
                'incident_id' => $drill['incident_id'],
                'runbook_id' => $drill['runbook']['runbook_id'],
                'decision_record' => $drill['runbook']['decision_record'],
                'assessment_score' => $drill['assessment']['score'],
            ],
            'impact' => [
                'Learner-facing configuration readiness became ambiguous because quality status drifted.',
                'Reviewers could no longer rely on the shared quality-gate shape without checking the runbook.',
                'Deployment confidence dropped until readiness, route documentation, and Pint checks passed again.',
            ],
            'root_cause' => [
                'Primary cause' => (string) config('labs.configuration.incident_root_cause'),
                'Detection gap' => 'The change passed review but was not rehearsed through the incident drill before release.',
                'Recovery factor' => 'The accepted decision record and runbook kept diagnosis focused on config drift before code refactors.',
            ],
            'action_items' => [
                [
                    'owner' => 'Configuration learner',
                    'task' => 'Add the recovered payload to release evidence and cite the postmortem id.',
                    'verification' => 'php artisan test --filter ConfigurationReleaseEvidenceTest',
                ],
                [
                    'owner' => 'Reviewer',
                    'task' => 'Check that future config PRs link assessment, decision record, runbook, and incident drill.',
                    'verification' => 'php artisan test --filter ConfigurationLearningPipelineTest',
                ],
                [
                    'owner' => 'Maintainer',
                    'task' => 'Promote repeated config drift into a remediation task instead of leaving it in notes.',
                    'verification' => 'php artisan test --filter ConfigurationRemediationPlanTest',
                ],
            ],
            'review_prompts' => [
                'Which signal first proved this was configuration drift, not controller behavior?',
                'Which command would have caught the failure before deploy?',
                'Which artifact should be updated if the config contract intentionally changes?',
                'What evidence belongs in spaced review so the recovery remains recallable?',
            ],
            'spaced_review_inputs' => [
                'Day 1: replay the incident timeline without looking at the drill page.',
                'Day 3: explain the root cause and smallest rollback path.',
                'Day 7: defend why the shared quality-gate shape prevents repeated ambiguity.',
            ],
            'evidence' => [
                ...$drill['recovery_evidence'],
                'Postmortem '.$this->postmortemLabel().' captures impact, root cause, action items, and review prompts.',
            ],
            'commands' => [
                ...$drill['commands'],
                'php artisan test --filter ConfigurationIncidentPostmortemTest',
            ],
            'handoff' => $drill['handoff'],
            'assessment' => $drill['assessment'],
        ];
    }

    /**
     * Return the reusable postmortem label for evidence strings.
     */
    private function postmortemLabel(): string
    {
        return (string) config('labs.configuration.ids.postmortem');
    }
}
