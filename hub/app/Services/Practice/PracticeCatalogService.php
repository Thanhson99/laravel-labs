<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Arr;

final class PracticeCatalogService
{
    /**
     * Return all configured practice tracks.
     *
     * @return array<int, array<string, mixed>>
     */
    public function tracks(): array
    {
        return array_values(config('practice.tracks', []));
    }

    /**
     * Return all configured practice exercises.
     *
     * @param  array{track?: string|null, search?: string|null}  $filters
     * @return array<int, array<string, mixed>>
     */
    public function exercises(array $filters = []): array
    {
        $search = mb_strtolower(trim((string) ($filters['search'] ?? '')));

        return collect(config('practice.exercises', []))
            ->when($filters['track'] ?? null, fn ($items, string $track) => $items->where('track', $track))
            ->when($search !== '', function ($items) use ($search) {
                return $items->filter(function (array $exercise) use ($search): bool {
                    $haystack = mb_strtolower(implode(' ', [
                        $exercise['title'] ?? '',
                        $exercise['objective'] ?? '',
                        $exercise['why'] ?? '',
                        $exercise['track'] ?? '',
                    ]));

                    return str_contains($haystack, $search);
                });
            })
            ->values()
            ->all();
    }

    /**
     * Find one practice exercise by slug.
     *
     * @return array<string, mixed>|null
     */
    public function findExercise(string $slug): ?array
    {
        return collect(config('practice.exercises', []))
            ->first(fn (array $exercise): bool => Arr::get($exercise, 'slug') === $slug);
    }

    /**
     * Return one track by slug.
     *
     * @return array<string, mixed>|null
     */
    public function findTrack(string $slug): ?array
    {
        return collect(config('practice.tracks', []))
            ->first(fn (array $track): bool => Arr::get($track, 'slug') === $slug);
    }
}
