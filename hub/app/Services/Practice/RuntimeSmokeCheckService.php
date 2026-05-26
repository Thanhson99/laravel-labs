<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class RuntimeSmokeCheckService
{
    /**
     * Build a runtime report for local PHP and Docker practice.
     *
     * @return array{status: string, ready: bool, checks: array<string, bool>, values: array<string, string>}
     */
    public function report(): array
    {
        $values = [
            'app_url' => (string) config('app.url'),
            'content_path' => (string) config('labs.content_path'),
            'session_driver' => (string) config('session.driver'),
            'cache_store' => (string) config('cache.default'),
            'queue_connection' => (string) config('queue.default'),
        ];

        $checks = [
            'dockerfile_exists' => is_file(base_path('Dockerfile')),
            'compose_file_exists' => is_file(base_path('docker-compose.yml')),
            'entrypoint_exists' => is_file(base_path('docker/entrypoint.sh')),
            'content_path_configured' => $values['content_path'] !== '',
            'file_session_enabled' => $values['session_driver'] === 'file',
            'file_cache_enabled' => $values['cache_store'] === 'file',
            'sync_queue_enabled' => $values['queue_connection'] === 'sync',
        ];

        $ready = ! in_array(false, $checks, true);

        return [
            'status' => $ready ? 'ready' : 'needs-work',
            'ready' => $ready,
            'checks' => $checks,
            'values' => $values,
        ];
    }
}
