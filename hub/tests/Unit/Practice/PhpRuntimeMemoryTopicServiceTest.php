<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\PhpRuntimeMemoryTopicService;
use PHPUnit\Framework\TestCase;

final class PhpRuntimeMemoryTopicServiceTest extends TestCase
{
    public function test_it_matches_source_records_tasks_labs_reviews_and_remediation(): void
    {
        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesRecord([
            'content' => [
                'title' => 'What is the difference between stack memory and heap memory?',
                'summary' => 'Explain call frames and heap-backed data.',
            ],
            'task' => 'Write a PHP runtime-memory note.',
        ]));

        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesSourceItems([
            [
                'content' => ['title' => 'Stack memory versus heap memory'],
                'task' => 'Explain references and cleanup.',
                'source' => ['path' => 'php/advanced.en.json'],
            ],
        ]));

        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesTasks([
            [
                'title' => 'Stack versus heap',
                'task' => 'Trace call frames.',
                'source_path' => 'php/advanced.en.json',
            ],
        ]));

        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesLab([
            'phases' => [
                [
                    'label' => 'Trace call frames',
                    'goal' => 'Model heap-backed data.',
                    'tasks' => ['Run runtime-memory checks.'],
                ],
            ],
        ]));

        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesReview([
            'cards' => [
                [
                    'recall_prompt' => 'Explain stack memory as active PHP call frames.',
                    'coding_action' => 'Write a large array note.',
                    'evidence_recheck' => 'Confirm proof.',
                ],
            ],
        ]));

        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesRemediation([
            'tasks' => [
                ['label' => 'Repair Call-frame model'],
                ['label' => 'Repair Heap-backed data'],
            ],
        ]));

        $this->assertTrue(PhpRuntimeMemoryTopicService::matchesPack([
            'artifact' => [
                'portfolio' => [
                    'source_coverage' => [
                        [
                            'title' => 'Stack versus heap',
                            'task' => 'Explain cleanup and references.',
                        ],
                    ],
                ],
            ],
        ], ['search' => 'stack memory']));
    }

    public function test_it_does_not_match_generic_php_content(): void
    {
        $this->assertFalse(PhpRuntimeMemoryTopicService::matchesRecord([
            'content' => [
                'title' => 'Normalize whitespace in a PHP string',
                'summary' => 'Use trim and preg_replace.',
            ],
            'task' => 'Write a normalizer.',
        ]));
    }
}
