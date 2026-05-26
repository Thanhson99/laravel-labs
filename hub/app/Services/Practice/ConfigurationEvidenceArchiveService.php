<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationEvidenceArchiveService
{
    /**
     * Create archive entries from configuration spaced review schedules.
     */
    public function __construct(
        private readonly ConfigurationSpacedReviewService $spacedReview,
    ) {}

    /**
     * Build a reusable archive entry for configuration practice evidence.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $review = $this->spacedReview->build();

        return [
            'title' => 'Configuration Evidence Archive',
            'summary' => 'Archive configuration practice evidence so app/auth contract work can be retrieved for future review, interviews, and portfolio notes.',
            'archive_id' => config('labs.configuration.archive_prefix').'-'.$review['decision'].'-'.$review['score'],
            'retrieval_keys' => [
                'config app auth readiness',
                'quality gate ready needs-work',
                'configuration rollback smoke checks',
                'config env contract interview',
                $review['incident_memory']['incident_id'].' '.$review['incident_memory']['postmortem_id'],
            ],
            'incident_archive' => [
                'incident_id' => $review['incident_memory']['incident_id'],
                'postmortem_id' => $review['incident_memory']['postmortem_id'],
                'root_cause' => $review['incident_memory']['root_cause'],
                'recovery_route' => $review['incident_memory']['recovery_route'],
                'review_inputs' => $review['incident_memory']['review_inputs'],
            ],
            'proof_bundle' => $this->proofBundle($review),
            'reuse_targets' => [
                'Portfolio note about treating Laravel configuration as a runtime contract.',
                'Interview answer about validating auth configuration before building protected progress.',
                'Review checklist for config cache, rollback, and quality-gate consistency.',
                'Incident recovery note about diagnosing configuration drift before changing controllers.',
            ],
            'retrieval_prompts' => [
                'What route proves the current config contract is ready?',
                'Which command proves style checks are part of the quality gate?',
                'What rollback action protects an auth provider change?',
                'How would you explain config() versus env() in this project?',
                'Which postmortem root cause should be replayed during spaced review?',
            ],
            'commands' => [
                ...$review['commands'],
                'php artisan test --filter ConfigurationEvidenceArchiveTest',
            ],
            'quality_gate' => $review['quality_gate'],
        ];
    }

    /**
     * Build proof bundle entries from spaced review cards and promotion criteria.
     *
     * @param  array<string, mixed>  $review
     * @return array<int, array{label: string, proof: string}>
     */
    private function proofBundle(array $review): array
    {
        $reviewProof = collect($review['review_cards'])
            ->map(fn (array $card): array => [
                'label' => 'Day '.$card['day'].': '.$card['focus'],
                'proof' => $card['route'].' -> '.$card['defense_prompt'],
            ]);

        $criteriaProof = collect($review['promotion_criteria'])
            ->map(fn (string $criterion): array => [
                'label' => 'Promotion criterion',
                'proof' => $criterion,
            ]);

        $incidentProof = collect($review['incident_memory']['review_inputs'])
            ->map(fn (string $input): array => [
                'label' => $review['incident_memory']['postmortem_id'],
                'proof' => $review['incident_memory']['recovery_route'].' -> '.$input,
            ]);

        return $reviewProof
            ->merge($criteriaProof)
            ->merge($incidentProof)
            ->values()
            ->all();
    }
}
