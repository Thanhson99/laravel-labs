<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\RuntimeSmokeCheckService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class RuntimeSmokeCheckServiceTest extends TestCase
{
    /**
     * The runtime report marks file-backed local settings as ready.
     */
    public function test_runtime_report_is_ready_for_file_backed_local_settings(): void
    {
        Config::set('labs.content_path', '../data');
        Config::set('session.driver', 'file');
        Config::set('cache.default', 'file');
        Config::set('queue.default', 'sync');

        $report = app(RuntimeSmokeCheckService::class)->report();

        $this->assertSame('ready', $report['status']);
        $this->assertTrue($report['ready']);
        $this->assertTrue($report['checks']['dockerfile_exists']);
        $this->assertSame('file', $report['values']['session_driver']);
    }

    /**
     * The runtime report exposes config drift as needs-work.
     */
    public function test_runtime_report_flags_config_drift(): void
    {
        Config::set('labs.content_path', '');
        Config::set('session.driver', 'database');
        Config::set('cache.default', 'database');
        Config::set('queue.default', 'database');

        $report = app(RuntimeSmokeCheckService::class)->report();

        $this->assertSame('needs-work', $report['status']);
        $this->assertFalse($report['ready']);
        $this->assertFalse($report['checks']['content_path_configured']);
        $this->assertFalse($report['checks']['file_session_enabled']);
    }
}
