<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\CoveringIndexTopicService;
use PHPUnit\Framework\TestCase;

final class CoveringIndexTopicServiceTest extends TestCase
{
    /**
     * Covering-index records are detected from title, body, task, or filters.
     */
    public function test_it_matches_covering_index_records(): void
    {
        $this->assertTrue(CoveringIndexTopicService::matchesRecord([
            'content' => [
                'title' => 'Covering Index: why an indexed query can still be slow',
                'body' => 'Index Only Scan can still perform heap fetch when visibility map is stale.',
            ],
            'task' => 'Create a covering-index and EXPLAIN verification plan.',
        ]));

        $this->assertTrue(CoveringIndexTopicService::matchesRecord([
            'content' => [
                'title' => 'Database performance note',
            ],
            'task' => 'Explain selected columns and included columns.',
        ], [
            'search' => 'visibility map',
        ]));

        $this->assertTrue(CoveringIndexTopicService::matchesRecord([
            'content' => [
                'title' => 'Database performance note',
            ],
            'task' => 'Run EXPLAIN ANALYZE BUFFERS and compare heap reads.',
        ]));

        $this->assertTrue(CoveringIndexTopicService::matchesRecord([
            'content' => [
                'title' => 'PostgreSQL index payload review',
            ],
            'task' => 'Review included columns, index bloat, autovacuum, and write overhead.',
        ]));
    }

    /**
     * Implementation tasks can also identify covering-index practice.
     */
    public function test_it_matches_covering_index_tasks(): void
    {
        $this->assertTrue(CoveringIndexTopicService::matchesTasks([
            [
                'title' => 'Covering Index: why an indexed query can still be slow',
                'task' => 'Create a covering-index and EXPLAIN verification plan.',
                'source_path' => 'laravel/performance-search.en.json',
                'files' => ['database/migrations/xxxx_xx_xx_add_orders_covering_index.php'],
            ],
        ]));

        $this->assertTrue(CoveringIndexTopicService::matchesTasks([], [
            'search' => 'Index Only Scan',
        ]));

        $this->assertTrue(CoveringIndexTopicService::matchesTasks([], [
            'search' => 'VACUUM ANALYZE',
        ]));
    }

    /**
     * Generic Eloquent topics should keep using the default database snippets.
     */
    public function test_it_does_not_match_generic_database_records(): void
    {
        $this->assertFalse(CoveringIndexTopicService::matchesRecord([
            'content' => [
                'title' => 'What is an Eloquent relationship?',
                'body' => 'Model belongsTo and hasMany relationships.',
            ],
            'task' => 'Create a migration/model/query example.',
        ]));
    }
}
