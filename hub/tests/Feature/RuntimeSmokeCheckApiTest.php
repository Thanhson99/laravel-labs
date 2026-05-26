<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class RuntimeSmokeCheckApiTest extends TestCase
{
    /**
     * The runtime smoke-check API returns Docker and environment readiness.
     */
    public function test_runtime_smoke_check_api_returns_readiness_report(): void
    {
        Config::set('labs.content_path', '../data');
        Config::set('session.driver', 'file');
        Config::set('cache.default', 'file');
        Config::set('queue.default', 'sync');

        $response = $this->getJson('/api/practice/runtime-smoke-check');

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'ready')
            ->assertJsonPath('data.ready', true)
            ->assertJsonPath('data.checks.dockerfile_exists', true)
            ->assertJsonPath('data.checks.compose_file_exists', true)
            ->assertJsonPath('data.values.queue_connection', 'sync');
    }
}
