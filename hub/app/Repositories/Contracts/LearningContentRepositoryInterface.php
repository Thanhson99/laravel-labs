<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface LearningContentRepositoryInterface
{
    /**
     * Return all JSON sources discovered from the configured content path.
     *
     * @return array<int, array<string, mixed>>
     */
    public function sources(): array;

    /**
     * Return a flattened question and topic item bank from all JSON content.
     *
     * @param  array{language?: string|null, family?: string|null, search?: string|null}  $filters
     * @return array<int, array<string, mixed>>
     */
    public function questions(array $filters = []): array;

    /**
     * Find one JSON source by its stable source key.
     *
     * @return array<string, mixed>|null
     */
    public function findSource(string $sourceKey): ?array;

    /**
     * Return integration statistics for the hub dashboard.
     *
     * @return array<string, int>
     */
    public function statistics(): array;
}
