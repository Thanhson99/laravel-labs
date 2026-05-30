<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\DatabaseLockingTopicService;
use PHPUnit\Framework\TestCase;

final class DatabaseLockingTopicServiceTest extends TestCase
{
    /**
     * Database-locking records are detected from title, body, task, or filters.
     */
    public function test_it_matches_database_locking_records(): void
    {
        $this->assertTrue(DatabaseLockingTopicService::matchesRecord([
            'content' => [
                'title' => 'Locking trong database là gì?',
                'body' => 'Use lockForUpdate, row lock, deadlock handling, and lock contention review.',
            ],
            'task' => 'Create a database locking plan for inventory updates.',
        ]));

        $this->assertTrue(DatabaseLockingTopicService::matchesRecord([
            'content' => [
                'title' => 'Concurrent request inventory example',
            ],
            'task' => 'Protect stock with DB::transaction and lock for update.',
        ]));

        $this->assertTrue(DatabaseLockingTopicService::matchesRecord([
            'content' => [
                'title' => 'Database consistency note',
            ],
            'task' => 'Explain row lock and table lock tradeoffs.',
        ], [
            'search' => 'race condition',
        ]));
    }

    /**
     * Implementation tasks can identify locking practice from filters or generated lab steps.
     */
    public function test_it_matches_database_locking_tasks(): void
    {
        $this->assertTrue(DatabaseLockingTopicService::matchesTasks([
            [
                'title' => 'What is database locking?',
                'task' => 'Implement lockForUpdate inside DB::transaction and handle deadlock retries.',
                'files' => ['app/Services/Practice/InventoryReservationService.php'],
            ],
        ]));

        $this->assertTrue(DatabaseLockingTopicService::matchesTasks([], [
            'search' => 'lock contention',
        ]));
    }

    /**
     * Generic database and index-performance topics should not become locking topics.
     */
    public function test_it_does_not_match_generic_database_or_covering_index_records(): void
    {
        $this->assertFalse(DatabaseLockingTopicService::matchesRecord([
            'content' => [
                'title' => 'What is an Eloquent relationship?',
                'body' => 'Model belongsTo and hasMany relationships.',
            ],
            'task' => 'Create a migration/model/query example.',
        ]));

        $this->assertFalse(DatabaseLockingTopicService::matchesRecord([
            'content' => [
                'title' => 'Covering Index: why an indexed query can still be slow',
                'body' => 'Index Only Scan, Heap Fetches, INCLUDE columns, and VACUUM.',
            ],
            'task' => 'Create a covering-index and EXPLAIN verification plan.',
        ]));
    }
}
