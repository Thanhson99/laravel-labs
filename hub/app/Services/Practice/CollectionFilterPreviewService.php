<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Collection;

final class CollectionFilterPreviewService
{
    /**
     * Filter and paginate a small in-memory dataset using Laravel collection patterns.
     *
     * @param  array{search: string, status: string, page: int, per_page: int}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, int|string>, applied_filters: array<string, string|int>}
     */
    public function preview(array $filters): array
    {
        $records = $this->records();
        $search = mb_strtolower(trim($filters['search']));
        $status = $filters['status'];

        $filtered = $records
            ->when($search !== '', function (Collection $items) use ($search): Collection {
                return $items->filter(function (array $item) use ($search): bool {
                    $haystack = mb_strtolower(implode(' ', [
                        $item['title'],
                        $item['owner'],
                        $item['technology'],
                    ]));

                    return str_contains($haystack, $search);
                });
            })
            ->when($status !== 'all', fn (Collection $items): Collection => $items->where('status', $status))
            ->values();

        $page = max(1, $filters['page']);
        $perPage = max(1, $filters['per_page']);
        $total = $filtered->count();

        return [
            'items' => $filtered
                ->forPage($page, $perPage)
                ->values()
                ->all(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) max(1, ceil($total / $perPage)),
            ],
            'applied_filters' => [
                'search' => $filters['search'],
                'status' => $status,
                'page' => $page,
                'per_page' => $perPage,
            ],
        ];
    }

    /**
     * Return the dataset used by the list-filter workbench.
     *
     * @return Collection<int, array{id: int, title: string, status: string, owner: string, technology: string}>
     */
    private function records(): Collection
    {
        return collect([
            ['id' => 1, 'title' => 'Add API validation tests', 'status' => 'open', 'owner' => 'learner', 'technology' => 'api-validation'],
            ['id' => 2, 'title' => 'Refactor Blade output escaping', 'status' => 'in-review', 'owner' => 'mentor', 'technology' => 'security'],
            ['id' => 3, 'title' => 'Document Docker cache settings', 'status' => 'done', 'owner' => 'learner', 'technology' => 'docker-runtime'],
            ['id' => 4, 'title' => 'Trace HTTP request flow', 'status' => 'open', 'owner' => 'learner', 'technology' => 'laravel-http'],
            ['id' => 5, 'title' => 'Normalize PHP input names', 'status' => 'done', 'owner' => 'mentor', 'technology' => 'php-foundation'],
        ]);
    }
}
