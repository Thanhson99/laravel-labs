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
     * Specialized Laravel technologies link to their closest runnable workbenches.
     */
    public function test_laravel_technologies_link_to_specialized_workbenches(): void
    {
        $service = new ContentPracticeWorkbenchLinkService;

        $expected = [
            'blade-ui' => '/workbench/security-escape-preview',
            'database-eloquent' => '/workbench/collection-filter-preview',
            'graphql-api' => '/workbench/graphql-rest-decision',
            'javascript-closures' => '/workbench/react-render-optimization-plan',
            'react-render-performance' => '/workbench/react-render-optimization-plan',
            'sql-injection-defense' => '/workbench/sql-injection-defense-plan',
            'csrf-protection' => '/workbench/csrf-protection-plan',
            'xss-defense' => '/workbench/security-escape-preview',
            'idor-access-control' => '/workbench/idor-access-review',
            'security-misconfiguration' => '/practice/configuration-readiness',
            'auth-security' => '/workbench/authorization-policy-plan',
            'files-media' => '/workbench/file-storage-plan',
            'async-workflow' => '/workbench/async-job-plan',
            'performance-cache' => '/workbench/cache-strategy-plan',
            'container-architecture' => '/workbench/layered-architecture-decision',
            'realtime-events' => '/workbench/event-listener-plan',
            'system-design-tradeoffs' => '/workbench/system-design-tradeoff-plan',
            'reverse-proxy-edge' => '/workbench/reverse-proxy-failure-plan',
            'siem-elk-observability' => '/workbench/siem-elk-plan',
            'load-balancing' => '/workbench/load-balancer-plan',
            'kubernetes-orchestration' => '/workbench/kubernetes-analogy-plan',
            'jwt-token-storage' => '/workbench/jwt-token-storage-plan',
            'jwt-revocation' => '/workbench/jwt-revocation-plan',
            'oauth-flow' => '/workbench/oauth-flow-plan',
            'graph-traversal' => '/workbench/graph-traversal-plan',
            'lsm-tree-storage' => '/workbench/lsm-tree-plan',
            'llm-foundations' => '/workbench/llm-decision-loop-plan',
            'rag-systems' => '/workbench/rag-strategy-plan',
            'ai-cloud-interview' => '/workbench/ai-cloud-interview-rubric',
            'ai-workflow' => '/workbench/ai-hallucination-guard-plan',
        ];

        foreach ($expected as $technology => $path) {
            $link = $service->linkFor($technology);

            $this->assertSame($path, $link['path'], "Unexpected workbench path for {$technology}.");
            $this->assertNotNull($link['route_name'], "Missing route name for {$technology}.");
            $this->assertNotSame('', $link['concept'], "Missing concept text for {$technology}.");
        }

        $this->assertStringContainsString('Client Credentials', $service->linkFor('oauth-flow')['concept']);
        $this->assertStringContainsString('Long Context', $service->linkFor('rag-systems')['concept']);
        $this->assertStringContainsString('CAG', $service->linkFor('rag-systems')['concept']);
    }

    /**
     * Hoisting content links to the dedicated JavaScript hoisting lab.
     */
    public function test_javascript_hoisting_content_links_to_hoisting_lab(): void
    {
        $link = (new ContentPracticeWorkbenchLinkService)->linkForItem([
            'technology' => 'javascript-closures',
            'content' => ['title' => 'JavaScript hoisting explained simply'],
            'task' => 'Create a JavaScript hoisting explanation.',
        ]);

        $this->assertSame('/workbench/javascript-hoisting-lab', $link['path']);
        $this->assertSame('practice.workbench.javascript-hoisting-lab', $link['route_name']);
        $this->assertStringContainsString('temporal dead zone', $link['concept']);
    }

    /**
     * AI agent memory content links to the dedicated memory planning workbench.
     */
    public function test_ai_agent_memory_content_links_to_memory_plan_workbench(): void
    {
        $link = (new ContentPracticeWorkbenchLinkService)->linkForItem([
            'technology' => 'llm-foundations',
            'content' => ['title' => '4 core AI agent memory types that help it work like a developer'],
            'task' => 'Create an AI agent memory plan.',
        ]);

        $this->assertSame('/workbench/ai-agent-memory-plan', $link['path']);
        $this->assertSame('practice.workbench.ai-agent-memory-plan', $link['route_name']);
        $this->assertStringContainsString('Working, episodic, semantic, and procedural memory', $link['concept']);
    }

    /**
     * Database locking content links to the dedicated concurrency planning workbench.
     */
    public function test_database_locking_context_links_to_locking_plan_workbench(): void
    {
        $service = new ContentPracticeWorkbenchLinkService;

        $itemLink = $service->linkForItem([
            'technology' => 'database-eloquent',
            'content' => ['title' => 'Locking trong database là gì?'],
            'task' => 'Explain DB::transaction, lockForUpdate, row locks, deadlock handling, and lock contention.',
        ]);
        $contextLink = $service->linkForTechnologyContext('database-eloquent', 'lockForUpdate deadlock row lock');

        $this->assertSame('/workbench/database-locking-plan', $itemLink['path']);
        $this->assertSame('practice.workbench.database-locking-plan', $itemLink['route_name']);
        $this->assertStringContainsString('lockForUpdate()', $itemLink['concept']);
        $this->assertSame('/workbench/database-locking-plan', $contextLink['path']);
    }

    /**
     * Unknown technologies do not invent fake workbench links.
     */
    public function test_unknown_technology_returns_null(): void
    {
        $this->assertNull((new ContentPracticeWorkbenchLinkService)->linkFor('unknown'));
    }
}
