<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyMasteryCheckpointService
{
    /**
     * Create mastery checkpoints from technology remediation plans.
     */
    public function __construct(
        private readonly TechnologyRemediationPlanService $remediationPlans,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a promote-or-repeat checkpoint for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $remediation = $this->remediationPlans->build($technology, $filters);

        return [
            'title' => sprintf('Technology Mastery Checkpoint: %s', $technology),
            'technology' => $technology,
            'remediation_plan' => $remediation,
            'decision' => [
                'promote_when' => 'All repair tasks have evidence, verification commands pass, and the oral explanation names concrete Laravel files.',
                'repeat_when' => 'Any repair task still depends on vague explanation, missing tests, or unverified changed files.',
            ],
            'proof_checklist' => $this->proofChecklist($remediation),
            'next_challenge' => [
                'label' => sprintf('Build a harder %s source-backed implementation without starter snippets.', $technology),
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation can be explained, tested, committed, and defended without reopening the generated code examples first.',
            ],
            'handoff' => [
                'next_session_goal' => sprintf('Start with the weakest remaining `%s` remediation task.', $technology),
                'first_action' => 'Open the remediation plan and rerun the first verification command.',
                'evidence_to_keep' => 'Save changed files, command output, and one concise explanation in the portfolio artifact.',
            ],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Confirm repair task evidence',
                'Run verification commands',
                'Make promote-or-repeat decision',
                'Choose next challenge',
                'Write next-session handoff',
            ]),
        ];
    }

    /**
     * Build proof items from remediation tasks and commands.
     *
     * @return array<int, string>
     */
    private function proofChecklist(array $remediation): array
    {
        $repairLabels = collect($remediation['tasks'])
            ->pluck('label')
            ->map(fn (string $label): string => sprintf('Evidence exists for `%s`.', $label))
            ->all();

        return [
            ...$repairLabels,
            sprintf('Verification commands were run: %s.', implode(' | ', $remediation['commands'])),
            'Next routes are known for implementation, interview defense, and reassessment.',
        ];
    }
}
