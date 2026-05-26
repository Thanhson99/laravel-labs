<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class PracticeTopicIntakeService
{
    /**
     * Build a stable practice topic response from validated learner input.
     *
     * @param  array{title: string, track: string, difficulty?: string}  $topic
     * @return array{id: string, title: string, track: string, difficulty: string, next_action: string, commands: array<int, string>}
     */
    public function create(array $topic): array
    {
        $track = $topic['track'];
        $title = trim($topic['title']);
        $difficulty = (string) ($topic['difficulty'] ?? 'beginner');

        return [
            'id' => Str::slug($track.' '.$title),
            'title' => $title,
            'track' => $track,
            'difficulty' => $difficulty,
            'next_action' => $this->nextActionFor($track),
            'commands' => $this->commandsFor($track),
        ];
    }

    /**
     * Return the next implementation action for the selected practice track.
     */
    private function nextActionFor(string $track): string
    {
        return match ($track) {
            'php-foundation' => 'Write the pure PHP function first, then add a unit test for edge cases.',
            'laravel-http' => 'Create the route and feature test first, then keep the controller thin.',
            'api-validation' => 'Write the API validation test first, then implement the Form Request and JSON response.',
            'testing-quality' => 'Write the failing test first, then implement the smallest behavior that makes it pass.',
            'docker-runtime' => 'Check the compose config first, then add a runtime smoke check for the environment.',
            default => 'Write the feature test first, then implement the smallest controller response.',
        };
    }

    /**
     * Return verification commands that fit the selected practice track.
     *
     * @return array<int, string>
     */
    private function commandsFor(string $track): array
    {
        return match ($track) {
            'docker-runtime' => [
                'docker-compose config',
                'docker-compose up --build --force-recreate',
                'php artisan test --filter RuntimeSmokeCheck',
            ],
            'testing-quality' => [
                'php artisan test',
                'vendor\\bin\\pint --test',
            ],
            default => [
                'php artisan test --filter PracticeTopic',
                'php artisan route:list --path=practice',
                'vendor\\bin\\pint --test',
            ],
        };
    }
}
