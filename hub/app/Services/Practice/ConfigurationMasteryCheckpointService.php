<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationMasteryCheckpointService
{
    /**
     * Create mastery checkpoints from configuration interview briefs.
     */
    public function __construct(
        private readonly ConfigurationInterviewBriefService $interviewBrief,
    ) {}

    /**
     * Build a mastery checkpoint for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $brief = $this->interviewBrief->build();
        $scorecard = $this->scorecard($brief);
        $score = collect($scorecard)->sum('points');

        return [
            'title' => 'Configuration Mastery Checkpoint',
            'summary' => 'Decide whether app/auth configuration practice is ready to promote, needs another implementation pass, or needs more interview rehearsal.',
            'score' => $score,
            'decision' => $score >= 85 && $brief['quality_gate']['passed'] ? 'promote' : 'repeat',
            'scorecard' => $scorecard,
            'repeat_triggers' => $this->repeatTriggers(),
            'next_actions' => $this->nextActions($score),
            'handoff' => [
                'next_route' => '/practice/configuration-readiness',
                'evidence_route' => '/practice/configuration-release-evidence',
                'interview_route' => '/practice/configuration-interview-brief',
            ],
            'commands' => [
                ...$brief['commands'],
                'php artisan test --filter ConfigurationMasteryCheckpointTest',
            ],
            'quality_gate' => $brief['quality_gate'],
        ];
    }

    /**
     * Build scored criteria from the current interview brief.
     *
     * @param  array<string, mixed>  $brief
     * @return array<int, array{criterion: string, points: int, proof: string}>
     */
    private function scorecard(array $brief): array
    {
        return [
            [
                'criterion' => 'Runtime contract is testable.',
                'points' => 25,
                'proof' => 'Configuration readiness and test plan routes expose app/auth assertions.',
            ],
            [
                'criterion' => 'Deployment path is reversible.',
                'points' => 20,
                'proof' => 'Deployment and release evidence include rollback steps.',
            ],
            [
                'criterion' => 'Quality signal is consistent.',
                'points' => $brief['quality_gate']['passed'] ? 25 : 0,
                'proof' => 'Quality gate status is '.$brief['quality_gate']['status'].'.',
            ],
            [
                'criterion' => 'Interview defense cites evidence.',
                'points' => count($brief['evidence_to_cite']) >= 4 ? 20 : 10,
                'proof' => 'Brief includes '.count($brief['evidence_to_cite']).' evidence references.',
            ],
            [
                'criterion' => 'Rehearsal checklist is concrete.',
                'points' => count($brief['rehearsal_checklist']) >= 4 ? 10 : 5,
                'proof' => 'Brief includes '.count($brief['rehearsal_checklist']).' rehearsal checks.',
            ],
        ];
    }

    /**
     * Return conditions that should force another practice pass.
     *
     * @return array<int, string>
     */
    private function repeatTriggers(): array
    {
        return [
            'Quality gate is needs-work.',
            'No rollback action can be stated for an auth or app config change.',
            'Interview answers do not cite a route, config key, or command.',
            'A config value is asserted even though it should vary per environment.',
        ];
    }

    /**
     * Return next actions based on score.
     *
     * @return array<int, string>
     */
    private function nextActions(int $score): array
    {
        if ($score >= 85) {
            return [
                'Promote to a protected progress or auth-flow implementation slice.',
                'Reuse the release evidence in a portfolio note.',
                'Rehearse the interview brief once with a five-minute time limit.',
            ];
        }

        return [
            'Repeat the configuration test plan and add one missing assertion.',
            'Revisit rollback notes for the weakest configuration area.',
            'Answer each interview question using one concrete proof point.',
        ];
    }
}
