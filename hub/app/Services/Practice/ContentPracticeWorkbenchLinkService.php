<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentPracticeWorkbenchLinkService
{
    /**
     * Return a workbench link that can specialize by mapped content title.
     *
     * @param  array<string, mixed>  $item
     * @return array{label: string, path: string, route_name: string|null, concept: string}|null
     */
    public function linkForItem(array $item): ?array
    {
        $title = strtolower((string) ($item['content']['title'] ?? ''));
        $task = strtolower((string) ($item['task'] ?? ''));

        if (($item['technology'] ?? null) === 'javascript-closures' && (str_contains($title, 'hoisting') || str_contains($task, 'hoisting'))) {
            return [
                'label' => 'Open JavaScript hoisting lab',
                'path' => '/workbench/javascript-hoisting-lab',
                'route_name' => 'practice.workbench.javascript-hoisting-lab',
                'concept' => 'Hoisting, var versus let/const, function declarations, function expressions, temporal dead zone, safer rewrites, and interview answers.',
            ];
        }

        if (($item['technology'] ?? null) === 'javascript-closures' && str_contains($title.' '.$task, 'arrow') && str_contains($title.' '.$task, 'this')) {
            return [
                'label' => 'Open JavaScript arrow this lab',
                'path' => '/workbench/javascript-arrow-this-lab',
                'route_name' => 'practice.workbench.javascript-arrow-this-lab',
                'concept' => 'Arrow-function lexical this, normal function call-site this, object-method traps, call/apply/bind limits, callbacks, and interview answers.',
            ];
        }

        if (($item['technology'] ?? null) === 'llm-foundations' && AiAgentMemoryTopicService::matchesRecord($item)) {
            return $this->agentMemoryLink();
        }

        if (($item['technology'] ?? null) === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($item)) {
            return $this->databaseLockingLink();
        }

        return $this->linkFor((string) ($item['technology'] ?? ''));
    }

    /**
     * Return the dedicated workbench link for AI agent memory records.
     *
     * @return array{label: string, path: string, route_name: string, concept: string}
     */
    public function agentMemoryLink(): array
    {
        return [
            'label' => 'Open AI agent memory workbench',
            'path' => '/workbench/ai-agent-memory-plan',
            'route_name' => 'practice.workbench.ai-agent-memory-plan',
            'concept' => 'Working, episodic, semantic, and procedural memory contracts with scope, freshness, confidence, permission, retention, and correction guardrails.',
        ];
    }

    /**
     * Return a workbench link that can specialize by technology and current search context.
     *
     * @return array{label: string, path: string, route_name: string|null, concept: string}|null
     */
    public function linkForTechnologyContext(string $technology, string $context): ?array
    {
        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($context)) {
            return $this->agentMemoryLink();
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord([], ['search' => $context])) {
            return $this->databaseLockingLink();
        }

        return $this->linkFor($technology);
    }

    /**
     * Return the dedicated workbench link for database-locking records.
     *
     * @return array{label: string, path: string, route_name: string, concept: string}
     */
    public function databaseLockingLink(): array
    {
        return [
            'label' => 'Open database locking workbench',
            'path' => '/workbench/database-locking-plan',
            'route_name' => 'practice.workbench.database-locking-plan',
            'concept' => 'Protected invariants, DB::transaction(), lockForUpdate(), indexed row locks, deadlock handling, contention, and lock-wait evidence.',
        ];
    }

    /**
     * Return the closest runnable workbench for a mapped content technology.
     *
     * @return array{label: string, path: string, route_name: string|null, concept: string}|null
     */
    public function linkFor(string $technology): ?array
    {
        return match ($technology) {
            'php' => [
                'label' => 'Open PHP name normalizer workbench',
                'path' => '/workbench/name-normalizer',
                'route_name' => 'practice.workbench.name-normalizer',
                'concept' => 'Typed PHP input normalization and unit-testable behavior.',
            ],
            'api-validation' => [
                'label' => 'Open API validation workbench',
                'path' => '/workbench/topic-intake',
                'route_name' => 'practice.workbench.topic-intake',
                'concept' => 'Form Request validation, thin API controller, and JSON response shape.',
            ],
            'restful-api-naming' => [
                'label' => 'Open RESTful API naming workbench',
                'path' => '/workbench/restful-api-naming-plan',
                'route_name' => 'practice.workbench.restful-api-naming-plan',
                'concept' => 'Resource naming, HTTP verb discipline, route names, query parameters, nested resources, and reviewable API contracts.',
            ],
            'graphql-api' => [
                'label' => 'Open GraphQL REST decision workbench',
                'path' => '/workbench/graphql-rest-decision',
                'route_name' => 'practice.workbench.graphql-rest-decision',
                'concept' => 'REST endpoint contracts, GraphQL schema/query shape, resolver boundaries, caching, and authorization tradeoffs.',
            ],
            'graph-traversal' => [
                'label' => 'Open graph traversal workbench',
                'path' => '/workbench/graph-traversal-plan',
                'route_name' => 'practice.workbench.graph-traversal-plan',
                'concept' => 'BFS and DFS traversal decisions for API crawling, tree menus, dependency graphs, cycle checks, and database hierarchy reads.',
            ],
            'javascript-closures' => [
                'label' => 'Open React render optimization workbench',
                'path' => '/workbench/react-render-optimization-plan',
                'route_name' => 'practice.workbench.react-render-optimization-plan',
                'concept' => 'Closure scope, captured bindings, callbacks, memoized functions, and hook dependency review.',
            ],
            'react-render-performance' => [
                'label' => 'Open React render optimization workbench',
                'path' => '/workbench/react-render-optimization-plan',
                'route_name' => 'practice.workbench.react-render-optimization-plan',
                'concept' => 'React.memo, useMemo, useCallback, profiler evidence, state locality, and virtualization tradeoffs.',
            ],
            'sql-injection-defense' => [
                'label' => 'Open SQL Injection defense workbench',
                'path' => '/workbench/sql-injection-defense-plan',
                'route_name' => 'practice.workbench.sql-injection-defense-plan',
                'concept' => 'Parameterized queries, bindings, raw SQL review, allowlisted identifiers, malicious payload tests, and interview answers.',
            ],
            'csrf-protection' => [
                'label' => 'Open CSRF protection workbench',
                'path' => '/workbench/csrf-protection-plan',
                'route_name' => 'practice.workbench.csrf-protection-plan',
                'concept' => 'CSRF tokens, SameSite cookies, state-changing method boundaries, Sanctum cookie bootstrap, and 419/stale-token tests.',
            ],
            'xss-defense' => [
                'label' => 'Open XSS escape preview workbench',
                'path' => '/workbench/security-escape-preview',
                'route_name' => 'practice.workbench.security-escape-preview',
                'concept' => 'Blade escaping, unsafe HTML detection, reflected/stored/DOM XSS review, and safe rendering boundaries.',
            ],
            'idor-access-control' => [
                'label' => 'Open IDOR access review workbench',
                'path' => '/workbench/idor-access-review',
                'route_name' => 'practice.workbench.idor-access-review',
                'concept' => 'Insecure Direct Object Reference, object-level authorization, tenant scoping, Laravel policies, attacker ID swapping, and two-user access tests.',
            ],
            'security-misconfiguration' => [
                'label' => 'Open configuration readiness lab',
                'path' => '/practice/configuration-readiness',
                'route_name' => 'practice.configuration-readiness',
                'concept' => 'Production-safe environment flags, secret exposure checks, CORS, security headers, trusted proxies, and deployment smoke checks.',
            ],
            'laravel-http' => [
                'label' => 'Open HTTP request-flow workbench',
                'path' => '/workbench/http-request-flow',
                'route_name' => 'practice.workbench.http-request-flow',
                'concept' => 'Route, validation, controller, service, and response flow.',
            ],
            'blade-ui' => [
                'label' => 'Open Blade security workbench',
                'path' => '/workbench/security-escape-preview',
                'route_name' => 'practice.workbench.security-escape-preview',
                'concept' => 'Blade output escaping, risky HTML detection, and safe preview behavior.',
            ],
            'database-eloquent' => [
                'label' => 'Open collection filter workbench',
                'path' => '/workbench/collection-filter-preview',
                'route_name' => 'practice.workbench.collection-filter-preview',
                'concept' => 'Eloquent-shaped filtering, query boundaries, pagination, and result review.',
            ],
            'testing-quality' => [
                'label' => 'Open quality-gate workbench',
                'path' => '/workbench/quality-gate',
                'route_name' => 'practice.workbench.quality-gate',
                'concept' => 'Focused tests, verification commands, and done criteria.',
            ],
            'auth-security' => [
                'label' => 'Open authorization policy workbench',
                'path' => '/workbench/authorization-policy-plan',
                'route_name' => 'practice.workbench.authorization-policy-plan',
                'concept' => 'Policy methods, controller authorization calls, denial behavior, and tests.',
            ],
            'files-media' => [
                'label' => 'Open file storage workbench',
                'path' => '/workbench/file-storage-plan',
                'route_name' => 'practice.workbench.file-storage-plan',
                'concept' => 'Validated upload lifecycle, storage disks, metadata, and cleanup decisions.',
            ],
            'async-workflow' => [
                'label' => 'Open async job workbench',
                'path' => '/workbench/async-job-plan',
                'route_name' => 'practice.workbench.async-job-plan',
                'concept' => 'Job boundaries, queue retry behavior, idempotency, and side-effect planning.',
            ],
            'performance-cache' => [
                'label' => 'Open cache strategy workbench',
                'path' => '/workbench/cache-strategy-plan',
                'route_name' => 'practice.workbench.cache-strategy-plan',
                'concept' => 'Cache keys, invalidation rules, freshness tolerance, and verification tests.',
            ],
            'container-architecture' => [
                'label' => 'Open layered architecture decision workbench',
                'path' => '/workbench/layered-architecture-decision',
                'route_name' => 'practice.workbench.layered-architecture-decision',
                'concept' => 'Architecture pressure, layer boundaries, and when Laravel structure should stay simple.',
            ],
            'realtime-events' => [
                'label' => 'Open event/listener workbench',
                'path' => '/workbench/event-listener-plan',
                'route_name' => 'practice.workbench.event-listener-plan',
                'concept' => 'Domain events, listeners, side effects, and broadcast-ready boundaries.',
            ],
            'system-design-tradeoffs' => [
                'label' => 'Open System Design tradeoff workbench',
                'path' => '/workbench/system-design-tradeoff-plan',
                'route_name' => 'practice.workbench.system-design-tradeoff-plan',
                'concept' => 'Clarifying questions, architecture costs, team maturity, operational complexity, and level-based interview framing.',
            ],
            'reverse-proxy-edge' => [
                'label' => 'Open proxy failure-mode workbench',
                'path' => '/workbench/reverse-proxy-failure-plan',
                'route_name' => 'practice.workbench.reverse-proxy-failure-plan',
                'concept' => 'Reverse proxy, shared edge request path, health gates, config rollout, blast radius, and origin reachability.',
            ],
            'siem-elk-observability' => [
                'label' => 'Open SIEM ELK workbench',
                'path' => '/workbench/siem-elk-plan',
                'route_name' => 'practice.workbench.siem-elk-plan',
                'concept' => 'SIEM, ELK roles, log shipping, parsing, detection rules, alert ownership, retention, privacy, and runbooks.',
            ],
            'load-balancing' => [
                'label' => 'Open load-balancer workbench',
                'path' => '/workbench/load-balancer-plan',
                'route_name' => 'practice.workbench.load-balancer-plan',
                'concept' => 'Round robin, weighted round robin, least connections, IP hash, and failure-mode tradeoffs.',
            ],
            'kubernetes-orchestration' => [
                'label' => 'Open Kubernetes workbench',
                'path' => '/workbench/kubernetes-analogy-plan',
                'route_name' => 'practice.workbench.kubernetes-analogy-plan',
                'concept' => 'Control plane, worker nodes, pods, services, ingress, probes, and rollout reasoning.',
            ],
            'jwt-token-storage' => [
                'label' => 'Open JWT storage workbench',
                'path' => '/workbench/jwt-token-storage-plan',
                'route_name' => 'practice.workbench.jwt-token-storage-plan',
                'concept' => 'Browser token storage, XSS and CSRF tradeoffs, refresh rotation, and production review.',
            ],
            'jwt-revocation' => [
                'label' => 'Open JWT revocation workbench',
                'path' => '/workbench/jwt-revocation-plan',
                'route_name' => 'practice.workbench.jwt-revocation-plan',
                'concept' => 'Denylist, token-version checks, short-lived access tokens, middleware, and failure modes.',
            ],
            'oauth-flow' => [
                'label' => 'Open OAuth flow workbench',
                'path' => '/workbench/oauth-flow-plan',
                'route_name' => 'practice.workbench.oauth-flow-plan',
                'concept' => 'Implicit Flow migration, Authorization Code with PKCE, Client Credentials, state validation, scopes, and token handling.',
            ],
            'lsm-tree-storage' => [
                'label' => 'Open LSM Tree workbench',
                'path' => '/workbench/lsm-tree-plan',
                'route_name' => 'practice.workbench.lsm-tree-plan',
                'concept' => 'Memtable, WAL, SSTables, compaction, Bloom filters, and read/write tradeoffs.',
            ],
            'llm-foundations' => [
                'label' => 'Open LLM decision-loop workbench',
                'path' => '/workbench/llm-decision-loop-plan',
                'route_name' => 'practice.workbench.llm-decision-loop-plan',
                'concept' => 'LLM probability, attention, Markov decision loops, reward models, PPO, feedback, and AGI claim guardrails.',
            ],
            'rag-systems' => [
                'label' => 'Open RAG strategy workbench',
                'path' => '/workbench/rag-strategy-plan',
                'route_name' => 'practice.workbench.rag-strategy-plan',
                'concept' => 'RAG, Long Context, CAG, hybrid routing, classic RAG, Graph RAG, Agentic RAG, retrieval contracts, citations, groundedness, freshness, cache controls, and tool-loop controls.',
            ],
            'ai-cloud-interview' => [
                'label' => 'Open AI Cloud interview rubric workbench',
                'path' => '/workbench/ai-cloud-interview-rubric',
                'route_name' => 'practice.workbench.ai-cloud-interview-rubric',
                'concept' => 'Concrete AI usage scoring, IaC review checks, source-of-truth verification, failure stories, and team prompt guardrails.',
            ],
            'docker-runtime' => [
                'label' => 'Open runtime smoke-check API',
                'path' => '/api/practice/runtime-smoke-check',
                'route_name' => 'api.practice.runtime-smoke-check',
                'concept' => 'Runtime configuration, Docker assumptions, and environment checks.',
            ],
            'ai-workflow' => [
                'label' => 'Open AI hallucination guard workbench',
                'path' => '/workbench/ai-hallucination-guard-plan',
                'route_name' => 'practice.workbench.ai-hallucination-guard-plan',
                'concept' => 'Evidence sources, claim checks, verification commands, and AI-review guardrails.',
            ],
            'interview' => [
                'label' => 'Open interview defense lab',
                'path' => '/practice/interview-defense-lab',
                'route_name' => 'practice.interview-defense-lab',
                'concept' => 'Turn interview answers into evidence-backed implementation defense.',
            ],
            default => null,
        };
    }
}
