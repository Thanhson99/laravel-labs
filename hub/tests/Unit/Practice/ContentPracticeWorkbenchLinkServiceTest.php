<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\ContentPracticeWorkbenchLinkService;
use PHPUnit\Framework\TestCase;

final class ContentPracticeWorkbenchLinkServiceTest extends TestCase
{
    /**
     * API validation content links to the runnable topic-intake workbench.
     */
    public function test_api_validation_links_to_topic_intake_workbench(): void
    {
        $link = (new ContentPracticeWorkbenchLinkService)->linkFor('api-validation');

        $this->assertSame('/workbench/topic-intake', $link['path']);
        $this->assertSame('practice.workbench.topic-intake', $link['route_name']);
        $this->assertStringContainsString('Form Request', $link['concept']);
    }

    /**
     * Docker runtime content links to the runtime smoke-check API.
     */
    public function test_docker_runtime_links_to_runtime_smoke_check_api(): void
    {
        $link = (new ContentPracticeWorkbenchLinkService)->linkFor('docker-runtime');

        $this->assertSame('/api/practice/runtime-smoke-check', $link['path']);
        $this->assertSame('api.practice.runtime-smoke-check', $link['route_name']);
    }

    /**
     * Unknown technologies do not invent fake workbench links.
     */
    public function test_unknown_technology_returns_null(): void
    {
        $this->assertNull((new ContentPracticeWorkbenchLinkService)->linkFor('unknown'));
    }
}
