<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationPortfolioReviewService
{
    /**
     * Score the configuration portfolio brief before reuse.
     */
    public function __construct(
        private readonly ConfigurationPortfolioBriefService $brief,
    ) {}

    /**
     * Build a scored review for the configuration portfolio brief.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $brief = $this->brief->build();
        $rubric = $this->rubric($brief);
        $score = collect($rubric)->sum('points');

        return [
            'title' => 'Configuration Portfolio Review',
            'summary' => 'Score the configuration portfolio brief for clarity, proof quality, interview readiness, and incident evidence before publishing or rehearsing it.',
            'archive_id' => $brief['archive_id'],
            'score' => $score,
            'result' => $score >= 90 ? 'publish-ready' : 'revise',
            'rubric' => $rubric,
            'review_notes' => [
                'The brief names a concrete Laravel configuration workflow instead of a generic learning claim.',
                'The proof table covers portfolio, interview, and incident recovery audiences.',
                'Security Misconfiguration evidence is represented as release-blocker proof, not a generic security checklist.',
                'Talking points connect app/auth configuration with quality-gate and recovery evidence.',
            ],
            'action_items' => [
                'Read the portfolio paragraph aloud and remove any claim that lacks proof.',
                'Keep one Security Misconfiguration release blocker beside the deployment or review note.',
                'Keep one source key beside every interview talking point.',
                'Use the incident recovery proof when asked about operational judgment.',
            ],
            'commands' => [
                ...$brief['commands'],
                'php artisan test --filter ConfigurationPortfolioReviewTest',
            ],
            'quality_gate' => $brief['quality_gate'],
        ];
    }

    /**
     * Build the review rubric from portfolio brief signals.
     *
     * @param  array<string, mixed>  $brief
     * @return array<int, array<string, mixed>>
     */
    private function rubric(array $brief): array
    {
        return [
            [
                'criterion' => 'Specific configuration claim',
                'points' => str_contains($brief['headline'], 'Laravel app/auth configuration') ? 25 : 15,
                'max_points' => 25,
                'evidence' => $brief['headline'],
            ],
            [
                'criterion' => 'Proof coverage',
                'points' => count($brief['proof_table']) >= 3 ? 25 : 15,
                'max_points' => 25,
                'evidence' => 'Proof table covers '.count($brief['proof_table']).' audiences.',
            ],
            [
                'criterion' => 'Interview readiness',
                'points' => count($brief['talking_points']) >= 3 ? 20 : 10,
                'max_points' => 20,
                'evidence' => 'Talking points are ready for oral defense.',
            ],
            [
                'criterion' => 'Review discipline',
                'points' => count($brief['review_checklist']) >= 4 ? 20 : 10,
                'max_points' => 20,
                'evidence' => 'Checklist protects proof quality before publishing.',
            ],
            [
                'criterion' => 'Security review evidence',
                'points' => $this->hasAudience($brief['proof_table'], 'Security review') ? 0 : 0,
                'max_points' => 0,
                'evidence' => 'Security Misconfiguration release blockers are included in proof coverage without changing the 100-point score.',
            ],
            [
                'criterion' => 'Quality status',
                'points' => $brief['quality_gate']['status'] === 'ready' ? 10 : 0,
                'max_points' => 10,
                'evidence' => 'Quality gate status is '.$brief['quality_gate']['status'].'.',
            ],
        ];
    }

    /**
     * Determine whether the proof table covers a target audience.
     *
     * @param  array<int, array<string, string>>  $proofTable
     */
    private function hasAudience(array $proofTable, string $audience): bool
    {
        return collect($proofTable)->contains(fn (array $row): bool => $row['audience'] === $audience);
    }
}
