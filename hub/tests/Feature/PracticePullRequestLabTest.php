<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticePullRequestLabTest extends TestCase
{
    /**
     * The PR lab page renders branch, commit, and PR draft data.
     */
    public function test_pull_request_lab_page_renders_pr_artifacts(): void
    {
        $response = $this->get('/practice/pull-request-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('PR lab packages practice work like a real Laravel change.')
            ->assertSee('practice/api-validation/what-is-an-api-in-a-laravel-context')
            ->assertSee('practice: implement what-is-an-api-in-a-laravel-context')
            ->assertSee('PR Progress Payload');
    }

    /**
     * The PR lab API returns branch, commit, pull request, and progress payload.
     */
    public function test_pull_request_lab_api_returns_pr_artifacts(): void
    {
        $response = $this->getJson('/api/practice/pull-request-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.branch', 'practice/api-validation/what-is-an-api-in-a-laravel-context')
            ->assertJsonPath('data.commit_message', 'practice: implement what-is-an-api-in-a-laravel-context lab')
            ->assertJsonPath('data.pull_request.changed_files.0', 'routes/api.php')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'branch',
                    'commit_message',
                    'pull_request' => [
                        'title',
                        'summary',
                        'changed_files',
                        'verification',
                        'review_checklist',
                    ],
                    'quality_gate_payload',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The PR lab API returns 404 for unknown records.
     */
    public function test_pull_request_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/pull-request-lab?record_id=missing-record');

        $response->assertNotFound();
    }
}
