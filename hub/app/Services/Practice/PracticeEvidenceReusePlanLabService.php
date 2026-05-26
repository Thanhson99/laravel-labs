<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeEvidenceReusePlanLabService
{
    /**
     * Build reuse plans from archive retrieval cards.
     */
    public function __construct(
        private readonly PracticeArchiveRetrievalLabService $retrievals,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create concrete reuse plans for retrieved Laravel practice evidence.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $retrievalLab = $this->retrievals->build($filters);

        $plans = collect($retrievalLab['retrieval_cards'])
            ->values()
            ->map(fn (array $card): array => [
                'plan' => $card['card'],
                'technology_segment' => $card['technology_segment'],
                'route_name' => $card['route_name'],
                'reuse_mode' => $this->reuseModeFor($card),
                'source_prompt' => $card['retrieval_prompt'],
                'portfolio_task' => $this->portfolioTaskFor($card),
                'interview_task' => $this->interviewTaskFor($card),
                'review_task' => $this->reviewTaskFor($card),
                'proof_inputs' => $card['proof_to_quote'],
                'quality_check' => $this->qualityCheckFor($card),
            ])
            ->all();

        return [
            'title' => 'Laravel evidence reuse plan practice lab',
            'source_retrieval_lab' => [
                'title' => $retrievalLab['title'],
                'card_count' => $retrievalLab['retrieval_summary']['card_count'],
                'portfolio_ready_count' => $retrievalLab['retrieval_summary']['portfolio_ready_count'],
                'refresh_required_count' => $retrievalLab['retrieval_summary']['refresh_required_count'],
            ],
            'reuse_rules' => [
                'Use one retrieved card for one concrete reuse artifact.',
                'Keep route proof and command proof together.',
                'Refresh evidence before reuse when the retrieval card requires it.',
                'End with a quality check that proves the artifact is specific.',
            ],
            'reuse_plans' => $plans,
            'reuse_summary' => [
                'plan_count' => count($plans),
                'portfolio_plan_count' => collect($plans)->where('reuse_mode', 'portfolio-and-interview')->count(),
                'refresh_plan_count' => collect($plans)->where('reuse_mode', 'refresh-first')->count(),
                'routes_reused' => collect($plans)->pluck('route_name')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $plans,
                fn (array $plan): string => sprintf('Reused evidence plan %d: %s', $plan['plan'], $plan['technology_segment'])
            ),
        ];
    }

    /**
     * Pick reuse mode from retrieval mode.
     *
     * @param  array{retrieval_mode: string}  $card
     */
    private function reuseModeFor(array $card): string
    {
        return $card['retrieval_mode'] === 'portfolio-ready'
            ? 'portfolio-and-interview'
            : 'refresh-first';
    }

    /**
     * Build a portfolio task from a retrieval card.
     *
     * @param  array{technology_segment: string, route_name: string}  $card
     */
    private function portfolioTaskFor(array $card): string
    {
        return sprintf('Write a portfolio note showing how `%s` proves %s practice.', $card['route_name'], $card['technology_segment']);
    }

    /**
     * Build an interview task from a retrieval card.
     *
     * @param  array{technology_segment: string, proof_to_quote: array<int, string>}  $card
     */
    private function interviewTaskFor(array $card): string
    {
        return sprintf('Prepare a 60-second answer about %s using this proof: %s', $card['technology_segment'], $card['proof_to_quote'][0]);
    }

    /**
     * Build a review task from a retrieval card.
     *
     * @param  array{refresh_check: array<string, mixed>}  $card
     */
    private function reviewTaskFor(array $card): string
    {
        return sprintf('Before reuse, apply refresh rule: %s', $card['refresh_check']['required_before_reuse']);
    }

    /**
     * Build a quality check for the reuse plan.
     *
     * @param  array{search_keys: array<int, string>, reuse_targets: array<int, string>}  $card
     * @return array<int, string>
     */
    private function qualityCheckFor(array $card): array
    {
        return [
            sprintf('Artifact includes at least two search keys: %s.', implode(', ', array_slice($card['search_keys'], 0, 2))),
            sprintf('Artifact serves this reuse target: %s', $card['reuse_targets'][0]),
            'Artifact names the route, command, and learner decision.',
        ];
    }
}
