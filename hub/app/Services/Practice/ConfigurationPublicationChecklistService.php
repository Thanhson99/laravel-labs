<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationPublicationChecklistService
{
    /**
     * Build publication guidance from the scored portfolio review.
     */
    public function __construct(
        private readonly ConfigurationPortfolioReviewService $review,
    ) {}

    /**
     * Build the publication checklist for configuration evidence.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $review = $this->review->build();

        return [
            'title' => 'Configuration Publication Checklist',
            'summary' => 'Decide whether the configuration portfolio artifact is ready to publish, rehearse, or hold for revision.',
            'archive_id' => $review['archive_id'],
            'publication_status' => $review['result'] === 'publish-ready' ? 'approved' : 'hold',
            'score' => $review['score'],
            'channels' => [
                [
                    'name' => 'Portfolio',
                    'output' => 'Publish the concise configuration runtime-contract paragraph with proof table references.',
                    'proof_required' => 'Archive id, readiness route, quality-gate status, and incident recovery source key.',
                ],
                [
                    'name' => 'Interview',
                    'output' => 'Rehearse the two-minute explanation with one config key, one route, and one command.',
                    'proof_required' => 'Talking point plus source key from the proof table.',
                ],
                [
                    'name' => 'Code review',
                    'output' => 'Use the checklist as a reviewer note for future app/auth configuration changes.',
                    'proof_required' => 'Quality status, route documentation check, and Pint result.',
                ],
            ],
            'pre_publish_checks' => [
                'Portfolio review score is at least 90.',
                'Every claim in the artifact has a source key or route.',
                'Incident recovery proof cites the postmortem id.',
                'Quality-gate status is ready.',
            ],
            'do_not_publish_if' => [
                'The artifact explains configuration without naming app/auth behavior.',
                'The proof table omits incident recovery evidence.',
                'Quality status is not ready.',
                'The learner cannot answer the interview prompt without rereading the page.',
            ],
            'commands' => [
                ...$review['commands'],
                'php artisan test --filter ConfigurationPublicationChecklistTest',
            ],
            'quality_gate' => $review['quality_gate'],
        ];
    }
}
