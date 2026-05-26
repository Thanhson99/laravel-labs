<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class AsyncJobPlanService
{
    /**
     * Build an async job plan from validated learner input.
     *
     * @param  array{job_name: string, payload_key: string, attempts: int, backoff_seconds: int}  $input
     * @return array{job: array<string, int|string>, idempotency_key: string, retry_policy: array<string, int|string>, implementation_steps: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $jobName = Str::studly($input['job_name']);
        $payloadKey = Str::slug($input['payload_key']);

        return [
            'job' => [
                'class' => $jobName,
                'payload_key' => $payloadKey,
                'queue' => 'default',
            ],
            'idempotency_key' => Str::slug(Str::kebab($jobName).' '.$payloadKey),
            'retry_policy' => [
                'attempts' => $input['attempts'],
                'backoff_seconds' => $input['backoff_seconds'],
                'failure_mode' => 'Record the failure, keep the payload inspectable, and retry only when the operation is idempotent.',
            ],
            'implementation_steps' => [
                'Create a Job class with typed constructor input.',
                'Validate and store the payload before dispatching the job.',
                'Use an idempotency key before calling external systems.',
                'Keep retries controlled with attempts and backoff.',
                'Add a feature test around dispatch intent and a unit test around idempotency rules.',
            ],
            'commands' => [
                'php artisan make:job '.$jobName,
                'php artisan queue:work --tries='.$input['attempts'],
                'php artisan queue:restart',
                'php artisan test --filter AsyncJobPlanWorkbenchTest',
            ],
        ];
    }
}
