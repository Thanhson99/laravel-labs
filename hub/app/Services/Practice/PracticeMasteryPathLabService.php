<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeMasteryPathLabService
{
    /**
     * Build multi-technology mastery paths from content-backed syllabuses.
     */
    public function __construct(
        private readonly ContentPracticeSyllabusService $syllabuses,
        private readonly PracticeProgressPayloadService $progressPayload,
        private readonly PracticeFilterNormalizerService $filters,
    ) {}

    /**
     * Create a mastery path across technology phases.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $phaseLimit = $this->filters->positiveInt($filters['phase_limit'] ?? null, 3);
        $tasksPerPhase = $this->filters->positiveInt($filters['tasks_per_phase'] ?? null, 2);

        $syllabus = $this->syllabuses->build([
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? 'api',
            'limit' => $filters['limit'] ?? 5,
        ]);

        $milestones = collect($syllabus['phases'])
            ->take($phaseLimit)
            ->map(fn (array $phase): array => [
                'phase' => $phase['phase'],
                'technology' => $phase['technology'],
                'title' => sprintf('Master %s through source-backed Laravel work', $phase['technology']),
                'record_count' => $phase['record_count'],
                'capstone_query' => $this->technologyQuery($phase['technology'], $filters, $tasksPerPhase),
                'checkpoint_query' => $this->technologyQuery($phase['technology'], $filters, $tasksPerPhase),
                'mentor_feedback_query' => $this->technologyQuery($phase['technology'], $filters, $tasksPerPhase),
                'done_when' => [
                    'Complete the capstone tasks for this technology.',
                    'Pass the checkpoint exam with source-backed evidence.',
                    'Answer mentor feedback and package one portfolio artifact.',
                ],
            ])
            ->values()
            ->all();

        return [
            'title' => 'Laravel content-backed mastery path',
            'milestones' => $milestones,
            'source_packs' => $syllabus['source_packs'],
            'meta' => [
                'filters' => [
                    'family' => $filters['family'] ?? 'laravel',
                    'language' => $filters['language'] ?? 'en',
                    'search' => $filters['search'] ?? 'api',
                    'limit' => $filters['limit'] ?? 5,
                    'phase_limit' => $phaseLimit,
                    'tasks_per_phase' => $tasksPerPhase,
                ],
                'milestone_count' => count($milestones),
                'source_pack_count' => count($syllabus['source_packs']),
                'record_count' => $syllabus['meta']['record_count'],
            ],
            'progress_payload' => $this->progressPayload->fromLabels(
                collect($milestones)
                    ->flatMap(fn (array $milestone): array => [
                        sprintf('Finish %s capstone', $milestone['technology']),
                        sprintf('Pass %s checkpoint exam', $milestone['technology']),
                        sprintf('Publish %s portfolio evidence', $milestone['technology']),
                    ])
            ),
        ];
    }

    /**
     * Build a shared query for technology-level labs.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function technologyQuery(string $technology, array $filters, int $limit): array
    {
        return [
            'technology' => $technology,
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? 'api',
            'limit' => $limit,
        ];
    }
}
