<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\PracticeProgressChecklistService;
use PHPUnit\Framework\TestCase;

final class PracticeProgressChecklistServiceTest extends TestCase
{
    /**
     * Partial checklist progress reports the next unfinished item.
     */
    public function test_partial_progress_reports_next_item(): void
    {
        $summary = (new PracticeProgressChecklistService)->summarize([
            ['label' => 'Write the request class', 'done' => true],
            ['label' => 'Write the controller', 'done' => false],
            ['label' => 'Run the feature test', 'done' => false],
        ]);

        $this->assertSame('in-progress', $summary['status']);
        $this->assertSame(1, $summary['completed']);
        $this->assertSame(3, $summary['total']);
        $this->assertSame(33, $summary['percent']);
        $this->assertSame('Write the controller', $summary['next_item']);
    }

    /**
     * Completed checklist progress is marked complete.
     */
    public function test_completed_progress_is_complete(): void
    {
        $summary = (new PracticeProgressChecklistService)->summarize([
            ['label' => 'Write code', 'done' => true],
            ['label' => 'Run tests', 'done' => true],
        ]);

        $this->assertSame('complete', $summary['status']);
        $this->assertSame(100, $summary['percent']);
        $this->assertNull($summary['next_item']);
    }
}
