<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeNextChallengeLabService
{
    /**
     * Build next challenge cards from competency maps.
     */
    public function __construct(
        private readonly PracticeCompetencyMapLabService $competencyMaps,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create challenge recommendations from competency readiness.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $map = $this->competencyMaps->build($filters);

        $challengeCards = collect($map['competencies'])
            ->map(fn (array $competency): array => [
                'technology_segment' => $competency['technology_segment'],
                'readiness' => $competency['readiness'],
                'challenge_type' => $this->challengeTypeFor($competency),
                'recommended_route' => $this->routeFor($competency),
                'why_this_challenge' => $this->reasonFor($competency),
                'verification_command' => $this->commandFor($competency),
                'evidence_to_submit' => $this->evidenceFor($competency),
            ])
            ->all();

        return [
            'title' => 'Laravel next challenge practice lab',
            'source_competency_lab' => [
                'title' => $map['title'],
                'technology_coverage' => $map['source_mastery_lab']['technology_coverage'],
                'competency_count' => count($map['competencies']),
            ],
            'challenge_rules' => [
                'Ready competencies move to harder labs.',
                'Weak competencies return to deliberate repetition.',
                'Every challenge must include a verification command.',
                'Evidence must prove code, explanation, and risk awareness.',
            ],
            'challenge_cards' => $challengeCards,
            'challenge_summary' => [
                'harder_lab_count' => collect($challengeCards)->where('challenge_type', 'harder-lab')->count(),
                'reinforcement_count' => collect($challengeCards)->where('challenge_type', 'reinforcement')->count(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $challengeCards,
                fn (array $card): string => sprintf('Accepted challenge: %s', $card['technology_segment'])
            ),
        ];
    }

    /**
     * Pick challenge type from competency readiness.
     *
     * @param  array{readiness: string}  $competency
     */
    private function challengeTypeFor(array $competency): string
    {
        return $competency['readiness'] === 'ready-for-harder-lab'
            ? 'harder-lab'
            : 'reinforcement';
    }

    /**
     * Pick the recommended practice route.
     *
     * @param  array{readiness: string}  $competency
     */
    private function routeFor(array $competency): string
    {
        return $competency['readiness'] === 'ready-for-harder-lab'
            ? 'practice.checkpoint-exam-lab'
            : 'practice.spaced-repetition-lab';
    }

    /**
     * Explain why the challenge is appropriate.
     *
     * @param  array{readiness: string, proof_summary: string}  $competency
     */
    private function reasonFor(array $competency): string
    {
        if ($competency['readiness'] === 'ready-for-harder-lab') {
            return sprintf('The competency has enough repeated proof: %s', $competency['proof_summary']);
        }

        return sprintf('The competency still needs reinforcement: %s', $competency['proof_summary']);
    }

    /**
     * Return the verification command for the challenge.
     *
     * @param  array{readiness: string}  $competency
     */
    private function commandFor(array $competency): string
    {
        return $competency['readiness'] === 'ready-for-harder-lab'
            ? 'php artisan test --filter PracticeCheckpointExamLabTest'
            : 'php artisan test --filter PracticeSpacedRepetitionLabTest';
    }

    /**
     * Return evidence required to complete the challenge.
     *
     * @param  array{next_action: string}  $competency
     * @return array<int, string>
     */
    private function evidenceFor(array $competency): array
    {
        return [
            $competency['next_action'],
            'Focused test result.',
            'One explanation of the Laravel layer decision.',
            'One risk or rollback note.',
        ];
    }
}
