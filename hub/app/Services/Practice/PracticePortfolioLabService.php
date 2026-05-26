<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticePortfolioLabService
{
    /**
     * Build portfolio artifacts from retrospective labs.
     */
    public function __construct(
        private readonly PracticeRetrospectiveLabService $retrospectives,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a portfolio entry for one content-backed practice implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $retrospective = $this->retrospectives->build($filters);

        if ($retrospective === null) {
            return null;
        }

        return [
            'title' => sprintf('Portfolio Lab: %s', $retrospective['record']['title']),
            'source' => $retrospective['source'],
            'technology' => $retrospective['technology'],
            'record' => $retrospective['record'],
            'portfolio_entry' => [
                'headline' => sprintf('Built a Laravel practice slice for %s', $retrospective['technology']),
                'problem' => sprintf('Practice record: %s', $retrospective['record']['title']),
                'source_reference' => $retrospective['source']['path'],
                'skills_practiced' => $this->skillsFor($retrospective['technology']),
                'evidence' => $retrospective['wins'],
                'next_improvement' => $retrospective['weak_spots'][0]['next_action'] ?? 'Pick one small improvement and verify it.',
            ],
            'writeup_template' => [
                'What I built',
                'Which source record/question drove the implementation',
                'Which Laravel layers changed',
                'How I verified the behavior',
                'What I would improve in the next pass',
            ],
            'next_labs' => $retrospective['next_labs'],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Write portfolio headline',
                'Explain source record and technology',
                'List evidence from tests and review',
                'Document one next improvement',
            ]),
        ];
    }

    /**
     * Return practiced skills for one technology.
     *
     * @return array<int, string>
     */
    private function skillsFor(string $technology): array
    {
        return match ($technology) {
            'api-validation' => ['API route design', 'Form Request validation', 'Thin controller', 'Service behavior', 'Feature testing'],
            'testing-quality' => ['Behavior-first testing', 'Quality gates', 'Review checklists', 'Verification evidence'],
            'docker-runtime' => ['Runtime configuration', 'Smoke checks', 'Environment verification'],
            default => ['Laravel routing', 'Service extraction', 'Feature tests', 'Practice review'],
        };
    }
}
