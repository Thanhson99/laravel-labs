<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeSessionPlannerService
{
    public function __construct(
        private readonly PracticeCatalogService $catalog,
    ) {}

    /**
     * Build a code-first practice session from native exercises.
     *
     * @return array{title: string, focus: string, exercises: array<int, array<string, mixed>>, commands: array<int, string>, done_when: array<int, string>}
     */
    public function today(?string $track = null): array
    {
        $filters = [
            'track' => $track,
            'search' => null,
        ];

        $exercises = collect($this->catalog->exercises($filters))
            ->take(3)
            ->values()
            ->all();

        return [
            'title' => 'Today Practice Session',
            'focus' => $track ?: 'all-tracks',
            'exercises' => $exercises,
            'commands' => [
                'php artisan test',
                'vendor\\bin\\pint --test',
                'php artisan route:list',
            ],
            'done_when' => [
                'At least one exercise has code changes.',
                'The related feature or unit test explains the behavior.',
                'The full test suite and Pint both pass.',
            ],
        ];
    }
}
