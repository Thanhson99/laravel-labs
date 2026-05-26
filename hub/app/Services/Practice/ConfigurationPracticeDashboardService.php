<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationPracticeDashboardService
{
    /**
     * Create a compact dashboard for the configuration practice pipeline.
     */
    public function __construct(
        private readonly ConfigurationLearningPipelineService $pipeline,
    ) {}

    /**
     * Build a dashboard summary for app/auth configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $pipeline = $this->pipeline->build();

        return [
            'title' => 'Configuration Practice Dashboard',
            'summary' => 'Track the app/auth configuration practice pipeline, current quality status, archived evidence, and next recommended stage from one screen.',
            'status' => [
                'quality' => $pipeline['quality_gate']['status'],
                'passed' => $pipeline['quality_gate']['passed'],
                'stage_count' => $pipeline['stage_count'],
                'archive_id' => $pipeline['archive']['archive_id'],
            ],
            'next_stage' => $this->nextStage($pipeline),
            'stage_groups' => $this->stageGroups($pipeline['stages']),
            'archive' => $pipeline['archive'],
            'progress_payload' => $pipeline['progress_payload'],
            'commands' => [
                'php artisan test --filter ConfigurationLearningPipelineTest',
                'php artisan test --filter ConfigurationPracticeDashboardTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Pick the next recommended stage from the pipeline state.
     *
     * @param  array<string, mixed>  $pipeline
     * @return array<string, mixed>
     */
    private function nextStage(array $pipeline): array
    {
        if (! $pipeline['quality_gate']['passed']) {
            return $pipeline['stages'][0];
        }

        return collect($pipeline['stages'])
            ->firstWhere('label', 'Assessment')
            ?? $pipeline['stages'][array_key_last($pipeline['stages'])];
    }

    /**
     * Group stages into scan-friendly dashboard sections.
     *
     * @param  array<int, array<string, mixed>>  $stages
     * @return array<int, array{name: string, stages: array<int, array<string, mixed>>}>
     */
    private function stageGroups(array $stages): array
    {
        $collection = collect($stages);

        return [
            [
                'name' => 'Build the contract',
                'stages' => $collection->slice(0, 3)->values()->all(),
            ],
            [
                'name' => 'Repair the risks',
                'stages' => $collection->slice(3, 8)->values()->all(),
            ],
            [
                'name' => 'Ship the change',
                'stages' => $collection->slice(11, 3)->values()->all(),
            ],
            [
                'name' => 'Prove mastery',
                'stages' => $collection->slice(14)->values()->all(),
            ],
        ];
    }
}
