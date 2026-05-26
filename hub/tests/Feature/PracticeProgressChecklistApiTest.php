<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeProgressChecklistApiTest extends TestCase
{
    /**
     * The progress checklist API summarizes submitted practice state.
     */
    public function test_progress_checklist_api_summarizes_progress(): void
    {
        $response = $this->postJson('/api/practice/progress-checklist', [
            'items' => [
                ['label' => 'Create Form Request', 'done' => true],
                ['label' => 'Create API Controller', 'done' => true],
                ['label' => 'Run feature test', 'done' => false],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'in-progress')
            ->assertJsonPath('data.completed', 2)
            ->assertJsonPath('data.total', 3)
            ->assertJsonPath('data.percent', 67)
            ->assertJsonPath('data.next_item', 'Run feature test');
    }

    /**
     * The progress checklist API validates checklist payload shape.
     */
    public function test_progress_checklist_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/progress-checklist', [
            'items' => [
                ['label' => 'No', 'done' => 'maybe'],
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.label', 'items.0.done']);
    }
}
