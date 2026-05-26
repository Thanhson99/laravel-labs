<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class AsyncJobPlanWorkbenchTest extends TestCase
{
    /**
     * The async job workbench renders the queue planning loop.
     */
    public function test_async_job_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/async-job-plan');

        $response
            ->assertOk()
            ->assertSee('Async Job Plan Workbench')
            ->assertSee('POST /api/practice/async-job-plan')
            ->assertSee('AsyncJobPlanService')
            ->assertSee('Plan async job');
    }

    /**
     * The async job API returns job, idempotency, retry, and command details.
     */
    public function test_async_job_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/async-job-plan', [
            'job_name' => 'Sync External Order',
            'payload_key' => 'order 123',
            'attempts' => 3,
            'backoff_seconds' => 60,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.job.class', 'SyncExternalOrder')
            ->assertJsonPath('data.job.payload_key', 'order-123')
            ->assertJsonPath('data.idempotency_key', 'sync-external-order-order-123')
            ->assertJsonPath('data.retry_policy.attempts', 3)
            ->assertJsonPath('data.commands.0', 'php artisan make:job SyncExternalOrder');
    }

    /**
     * Invalid async job payloads return validation errors.
     */
    public function test_async_job_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/async-job-plan', [
            'job_name' => '<bad>',
            'payload_key' => 'x',
            'attempts' => 0,
            'backoff_seconds' => 0,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['job_name', 'payload_key', 'attempts', 'backoff_seconds']);
    }
}
