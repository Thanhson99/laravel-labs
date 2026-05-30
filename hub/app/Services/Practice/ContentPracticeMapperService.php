<?php

declare(strict_types=1);

namespace App\Services\Practice;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Support\Str;

final class ContentPracticeMapperService
{
    /**
     * Create a mapper from learning content records to native practice exercises.
     */
    public function __construct(
        private readonly LearningContentRepositoryInterface $content,
        private readonly PracticeCatalogService $catalog,
    ) {}

    /**
     * Build content-to-practice mappings from JSON question records.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function map(array $filters = []): array
    {
        $limit = $this->normalizeLimit($filters['limit'] ?? null);
        $technology = $filters['technology'] ?? null;
        $sourceKey = $filters['source_key'] ?? null;
        $recordId = $filters['record_id'] ?? null;

        $records = collect($this->content->questions([
            'family' => $filters['family'] ?? null,
            'language' => $filters['language'] ?? null,
            'search' => $filters['search'] ?? null,
        ]))
            ->filter(fn (array $record): bool => filled($record['title'] ?? null))
            ->when($sourceKey, fn ($items, string $value) => $items->where('source_key', $value))
            ->when($recordId, fn ($items, string $value) => $items->where('id', $value))
            ->map(fn (array $record): array => $this->toPracticeItem($record))
            ->when($technology, fn ($items, string $value) => $items->where('technology', $value))
            ->take($limit)
            ->values()
            ->all();

        return [
            'items' => $records,
            'meta' => [
                'filters' => [
                    'family' => $filters['family'] ?? null,
                    'language' => $filters['language'] ?? null,
                    'search' => $filters['search'] ?? null,
                    'technology' => $technology,
                    'source_key' => $sourceKey,
                    'record_id' => $recordId,
                    'limit' => $limit,
                ],
                'count' => count($records),
                'technologies' => $this->technologies(),
            ],
        ];
    }

    /**
     * Return technology filters supported by the mapper.
     *
     * @return array<int, string>
     */
    public function technologies(): array
    {
        return [
            'php',
            'laravel-http',
            'blade-ui',
            'database-eloquent',
            'api-validation',
            'restful-api-naming',
            'graphql-api',
            'graph-traversal',
            'javascript-closures',
            'react-render-performance',
            'sql-injection-defense',
            'csrf-protection',
            'xss-defense',
            'idor-access-control',
            'security-misconfiguration',
            'auth-security',
            'files-media',
            'async-workflow',
            'performance-cache',
            'container-architecture',
            'realtime-events',
            'system-design-tradeoffs',
            'reverse-proxy-edge',
            'siem-elk-observability',
            'load-balancing',
            'kubernetes-orchestration',
            'jwt-token-storage',
            'jwt-revocation',
            'oauth-flow',
            'lsm-tree-storage',
            'llm-foundations',
            'rag-systems',
            'ai-cloud-interview',
            'testing-quality',
            'docker-runtime',
            'ai-workflow',
            'interview',
        ];
    }

    /**
     * Infer a practice technology for a raw content record.
     */
    public function inferTechnology(array $record): string
    {
        return $this->technologyFor($record);
    }

    /**
     * Return the native practice summary for an inferred technology.
     *
     * @return array{slug: string|null, title: string, track: string}
     */
    public function practiceSummaryFor(string $technology): array
    {
        $exercise = $this->exerciseFor($technology);

        return [
            'slug' => $exercise['slug'] ?? null,
            'title' => $exercise['title'] ?? 'Create a new practice exercise',
            'track' => $exercise['track'] ?? $technology,
        ];
    }

    /**
     * Convert one content record to a code-first practice item.
     *
     * @return array<string, mixed>
     */
    private function toPracticeItem(array $record): array
    {
        $technology = $this->technologyFor($record);

        return [
            'id' => (string) $record['id'],
            'technology' => $technology,
            'source' => [
                'key' => $record['source_key'],
                'path' => $record['source_path'],
                'family' => $record['family'],
                'topic' => $record['topic'],
                'language' => $record['language'],
                'title' => $record['source_title'],
            ],
            'content' => [
                'title' => $record['title'],
                'type' => $record['type'],
                'group' => $record['group'],
            ],
            'practice' => [
                ...$this->practiceSummaryFor($technology),
            ],
            'task' => $this->taskFor($technology, (string) $record['title']),
        ];
    }

    /**
     * Infer the closest practice technology from content text and source path.
     */
    private function technologyFor(array $record): string
    {
        $haystack = Str::lower(implode(' ', [
            $record['family'] ?? '',
            $record['topic'] ?? '',
            $record['source_path'] ?? '',
            $record['title'] ?? '',
            $record['body'] ?? '',
            $record['answer'] ?? '',
        ]));

        return match (true) {
            str_contains($haystack, 'idor') || str_contains($haystack, 'insecure direct object reference') || str_contains($haystack, 'object-level authorization') || str_contains($haystack, 'bola') || str_contains($haystack, 'object id') => 'idor-access-control',
            str_contains($haystack, 'sql injection') || str_contains($haystack, 'parameterized query') || str_contains($haystack, 'prepared statement') || str_contains($haystack, 'query binding') || str_contains($haystack, 'bindings') => 'sql-injection-defense',
            str_contains($haystack, 'stack memory') || str_contains($haystack, 'heap memory') || str_contains($haystack, 'vùng nhớ stack') || str_contains($haystack, 'vùng nhớ heap') || str_contains($haystack, 'call frame') || str_contains($haystack, 'memory allocation') => 'php',
            ($record['family'] ?? '') === 'php' => 'php',
            str_contains($haystack, 'covering index') || str_contains($haystack, 'index only scan') || str_contains($haystack, 'heap fetch') || str_contains($haystack, 'visibility map') || str_contains($haystack, 'included columns') || str_contains($haystack, 'vacuum') => 'database-eloquent',
            str_contains($haystack, 'database locking') || str_contains($haystack, 'lockforupdate') || str_contains($haystack, 'row lock') || str_contains($haystack, 'deadlock') || str_contains($haystack, 'lock contention') || str_contains($haystack, 'race condition') => 'database-eloquent',
            str_contains($haystack, 'lsm tree') || str_contains($haystack, 'sstable') || str_contains($haystack, 'storage engine') => 'lsm-tree-storage',
            str_contains($haystack, 'oauth') || str_contains($haystack, 'implicit flow') || str_contains($haystack, 'pkce') || str_contains($haystack, 'authorization code') || str_contains($haystack, 'client credentials') || str_contains($haystack, 'machine-to-machine') || str_contains($haystack, 'service-to-service') => 'oauth-flow',
            str_contains($haystack, 'jwt') && (str_contains($haystack, 'revocation') || str_contains($haystack, 'denylist') || str_contains($haystack, 'token-version')) => 'jwt-revocation',
            str_contains($haystack, 'jwt') || str_contains($haystack, 'httponly') || str_contains($haystack, 'localstorage') || str_contains($haystack, 'token storage') => 'jwt-token-storage',
            str_contains($haystack, 'csrf') || str_contains($haystack, 'cross-site request forgery') || str_contains($haystack, 'samesite') || str_contains($haystack, '@csrf') || str_contains($haystack, 'xsrf') || str_contains($haystack, '419') => 'csrf-protection',
            str_contains($haystack, 'xss') || str_contains($haystack, 'cross-site scripting') || str_contains($haystack, 'reflected xss') || str_contains($haystack, 'stored xss') || str_contains($haystack, 'dom-based xss') || str_contains($haystack, 'output escaping') || str_contains($haystack, 'raw html') || str_contains($haystack, '{!!') || str_contains($haystack, 'content security policy') || str_contains($haystack, 'csp') => 'xss-defense',
            str_contains($haystack, 'security misconfiguration') || str_contains($haystack, 'misconfiguration') || str_contains($haystack, 'app_debug') || str_contains($haystack, 'debug mode') || str_contains($haystack, '.env') || str_contains($haystack, 'exposed secrets') || str_contains($haystack, 'public storage bucket') || str_contains($haystack, 'security headers') || str_contains($haystack, 'trusted proxies') || str_contains($haystack, 'cors') => 'security-misconfiguration',
            str_contains($haystack, 'broken authentication') || str_contains($haystack, 'lỗi authentication') || str_contains($haystack, 'session fixation') || str_contains($haystack, 'password reset') || str_contains($haystack, 'reset token') || str_contains($haystack, 'remember-me') || str_contains($haystack, 'brute force') || str_contains($haystack, 'mfa recovery') => 'auth-security',
            str_contains($haystack, 'kubernetes') || str_contains($haystack, 'control plane') || str_contains($haystack, 'worker node') || str_contains($haystack, 'pod') => 'kubernetes-orchestration',
            str_contains($haystack, 'system design') || str_contains($haystack, 'tradeoff') || str_contains($haystack, 'notification system') || str_contains($haystack, 'long polling') || str_contains($haystack, 'strong technical depth') || str_contains($haystack, 'team maturity') || str_contains($haystack, 'who pays') || str_contains($haystack, 'ai chịu') => 'system-design-tradeoffs',
            str_contains($haystack, 'reverse proxy') || str_contains($haystack, 'cloudflare') || str_contains($haystack, 'edge layer') || str_contains($haystack, 'core proxy') || str_contains($haystack, 'feature file') || str_contains($haystack, 'http 500') => 'reverse-proxy-edge',
            str_contains($haystack, 'siem') || str_contains($haystack, 'elk stack') || str_contains($haystack, 'elasticsearch') || str_contains($haystack, 'logstash') || str_contains($haystack, 'kibana') || str_contains($haystack, 'beats') || str_contains($haystack, 'elastic agent') || str_contains($haystack, 'security information and event management') => 'siem-elk-observability',
            str_contains($haystack, 'load balancer') || str_contains($haystack, 'round robin') || str_contains($haystack, 'least connections') || str_contains($haystack, 'ip hash') || str_contains($haystack, 'sticky session') => 'load-balancing',
            str_contains($haystack, 'bfs') || str_contains($haystack, 'dfs') || str_contains($haystack, 'breadth-first') || str_contains($haystack, 'depth-first') || str_contains($haystack, 'breadth first') || str_contains($haystack, 'depth first') || str_contains($haystack, 'graph traversal') || str_contains($haystack, 'tree traversal') => 'graph-traversal',
            str_contains($haystack, 'rag') || str_contains($haystack, 'retrieval augmented') || str_contains($haystack, 'graph rag') || str_contains($haystack, 'agentic rag') || str_contains($haystack, 'long context') || str_contains($haystack, 'cag') || str_contains($haystack, 'cache-augmented') || str_contains($haystack, 'context strategy') || str_contains($haystack, 'retrieval pipeline') || str_contains($haystack, 'grounded answer') || str_contains($haystack, 'citation coverage') => 'rag-systems',
            str_contains($haystack, 'large language model') || str_contains($haystack, 'llm') || str_contains($haystack, 'predictive ai') || str_contains($haystack, 'generative ai') || str_contains($haystack, 'ai agent memory') || str_contains($haystack, 'agent memory') || str_contains($haystack, 'working memory') || str_contains($haystack, 'episodic memory') || str_contains($haystack, 'semantic memory') || str_contains($haystack, 'procedural memory') || str_contains($haystack, 'forecast') || str_contains($haystack, 'classification') || str_contains($haystack, 'markov') || str_contains($haystack, 'ppo') || str_contains($haystack, 'reinforcement learning') || str_contains($haystack, 'reward model') || str_contains($haystack, 'agi') => 'llm-foundations',
            str_contains($haystack, 'cloud engineer') || str_contains($haystack, 'terraform') || str_contains($haystack, 'cloudformation') || str_contains($haystack, 'aws documentation') || str_contains($haystack, 'claude.md') || str_contains($haystack, 'system prompt') || str_contains($haystack, 'thought partner') => 'ai-cloud-interview',
            (str_contains($haystack, 'javascript') && str_contains($haystack, 'hoisting')) || str_contains($haystack, 'temporal dead zone') || str_contains($haystack, 'function declaration') || str_contains($haystack, 'function expression') => 'javascript-closures',
            ($record['family'] ?? '') === 'interview' => 'interview',
            str_contains($haystack, 'test') || str_contains($haystack, 'pint') || str_contains($haystack, 'quality') => 'testing-quality',
            str_contains($haystack, 'container') || str_contains($haystack, 'binding') || str_contains($haystack, 'provider') || str_contains($haystack, 'dependency injection') || str_contains($haystack, 'layered architecture') || str_contains($haystack, 'clean architecture') => 'container-architecture',
            str_contains($haystack, 'graphql') || str_contains($haystack, 'resolver') || str_contains($haystack, 'overfetch') || str_contains($haystack, 'underfetch') || str_contains($haystack, 'schema/query') => 'graphql-api',
            str_contains($haystack, 'restful api naming') || str_contains($haystack, 'restful naming') || str_contains($haystack, 'endpoint naming') || str_contains($haystack, 'đặt tên endpoint') || str_contains($haystack, 'resource nouns') || str_contains($haystack, 'danh từ resource') || str_contains($haystack, 'plural resource') || str_contains($haystack, 'resource số nhiều') || str_contains($haystack, 'nested resource') => 'restful-api-naming',
            (str_contains($haystack, 'javascript') && str_contains($haystack, 'closure')) || str_contains($haystack, 'arrow function') || str_contains($haystack, 'lexical this') || str_contains($haystack, 'call-site this') || str_contains($haystack, 'object method') || str_contains($haystack, 'lexical scope') || str_contains($haystack, 'function factory') || str_contains($haystack, 'private variable') || str_contains($haystack, 'stale closure') || str_contains($haystack, 'debounce') || str_contains($haystack, 'throttle') => 'javascript-closures',
            str_contains($haystack, 'react') && (str_contains($haystack, 'memo') || str_contains($haystack, 'usememo') || str_contains($haystack, 'usecallback') || str_contains($haystack, 're-render') || str_contains($haystack, 'profiler')) => 'react-render-performance',
            str_contains($haystack, 'api') || str_contains($haystack, 'request') || str_contains($haystack, 'validation') || str_contains($haystack, 'sanctum') => 'api-validation',
            str_contains($haystack, 'policy') || str_contains($haystack, 'gate') || str_contains($haystack, 'auth') || str_contains($haystack, 'security') || str_contains($haystack, 'csrf') || str_contains($haystack, 'xss') => 'auth-security',
            str_contains($haystack, 'migration') || str_contains($haystack, 'eloquent') || str_contains($haystack, 'database') || str_contains($haystack, 'model') || str_contains($haystack, 'query') => 'database-eloquent',
            str_contains($haystack, 'upload') || str_contains($haystack, 'file') || str_contains($haystack, 'storage') || str_contains($haystack, 'media') || str_contains($haystack, 'filesystem') => 'files-media',
            str_contains($haystack, 'queue') || str_contains($haystack, 'job') || str_contains($haystack, 'event') || str_contains($haystack, 'listener') || str_contains($haystack, 'mail') || str_contains($haystack, 'notification') => 'async-workflow',
            str_contains($haystack, 'cache') || str_contains($haystack, 'performance') || str_contains($haystack, 'meilisearch') || str_contains($haystack, 'search') || str_contains($haystack, 'index') => 'performance-cache',
            str_contains($haystack, 'broadcast') || str_contains($haystack, 'websocket') || str_contains($haystack, 'realtime') || str_contains($haystack, 'pusher') => 'realtime-events',
            str_contains($haystack, 'docker') || str_contains($haystack, 'sail') || str_contains($haystack, 'deploy') || str_contains($haystack, 'devops') => 'docker-runtime',
            str_contains($haystack, 'blade') || str_contains($haystack, 'livewire') || str_contains($haystack, 'vite') || str_contains($haystack, 'frontend') || str_contains($haystack, 'alpine') => 'blade-ui',
            str_contains($haystack, 'blade') || str_contains($haystack, 'controller') || str_contains($haystack, 'route') => 'laravel-http',
            preg_match('/\bai\b/', $haystack) === 1 || str_contains($haystack, 'prompt') || str_contains($haystack, 'review') => 'ai-workflow',
            default => 'laravel-http',
        };
    }

    /**
     * Find the current native exercise that best matches a technology.
     *
     * @return array<string, mixed>|null
     */
    private function exerciseFor(string $technology): ?array
    {
        $slug = match ($technology) {
            'php' => 'php-cli-input-normalizer',
            'api-validation' => 'api-form-request-slice',
            'restful-api-naming' => 'restful-api-naming-review',
            'graphql-api' => 'graphql-rest-decision-workbench',
            'graph-traversal' => 'graph-traversal-plan-workbench',
            'javascript-closures' => 'react-render-optimization-workbench',
            'react-render-performance' => 'react-render-optimization-workbench',
            'sql-injection-defense' => 'sql-injection-defense-plan-workbench',
            'csrf-protection' => 'csrf-protection-plan-workbench',
            'xss-defense' => 'blade-escaping-xss-preview',
            'idor-access-control' => 'idor-access-review-workbench',
            'security-misconfiguration' => 'authorization-policy-plan-workbench',
            'auth-security' => 'authorization-policy-plan-workbench',
            'files-media' => 'file-storage-plan-workbench',
            'async-workflow' => 'async-job-plan-workbench',
            'performance-cache' => 'cache-strategy-plan-workbench',
            'container-architecture' => 'clean-architecture-layering-tradeoff',
            'realtime-events' => 'event-listener-plan-workbench',
            'system-design-tradeoffs' => 'system-design-tradeoff-plan-workbench',
            'reverse-proxy-edge' => 'reverse-proxy-failure-plan-workbench',
            'siem-elk-observability' => 'siem-elk-plan-workbench',
            'load-balancing' => 'load-balancer-algorithm-plan-workbench',
            'kubernetes-orchestration' => 'kubernetes-analogy-plan-workbench',
            'jwt-token-storage' => 'jwt-token-storage-plan-workbench',
            'jwt-revocation' => 'jwt-revocation-plan-workbench',
            'oauth-flow' => 'oauth-flow-plan-workbench',
            'lsm-tree-storage' => 'lsm-tree-plan-workbench',
            'llm-foundations' => 'llm-decision-loop-plan-workbench',
            'rag-systems' => 'rag-strategy-plan-workbench',
            'ai-cloud-interview' => 'ai-cloud-interview-rubric-workbench',
            'testing-quality' => 'feature-test-route-behavior',
            'docker-runtime' => 'docker-compose-smoke-check',
            'ai-workflow' => 'ai-hallucination-guard-plan-workbench',
            default => 'laravel-thin-controller',
        };

        return $this->catalog->findExercise($slug);
    }

    /**
     * Build one concrete coding task from a content topic.
     */
    private function taskFor(string $technology, string $title): string
    {
        if ($technology === 'php' && (str_contains(Str::lower($title), 'stack') || str_contains(Str::lower($title), 'heap'))) {
            return sprintf('Write a PHP runtime-memory note and small example for: %s', $title);
        }

        if ($technology === 'javascript-closures' && str_contains(Str::lower($title), 'hoisting')) {
            return sprintf('Create a JavaScript hoisting explanation, visual code example, and interview checklist for: %s', $title);
        }

        if ($technology === 'javascript-closures' && (str_contains(Str::lower($title), 'arrow') || str_contains(Str::lower($title), 'this'))) {
            return sprintf('Create a JavaScript arrow-function `this` comparison, code example, and interview checklist for: %s', $title);
        }

        if ($technology === 'auth-security' && (str_contains(Str::lower($title), 'broken authentication') || str_contains(Str::lower($title), 'authentication'))) {
            return sprintf('Create a broken authentication lifecycle review for: %s', $title);
        }

        return match ($technology) {
            'php' => sprintf('Write a typed PHP function that demonstrates: %s', $title),
            'api-validation' => sprintf('Create a Form Request and API response for: %s', $title),
            'restful-api-naming' => sprintf('Create a RESTful endpoint naming review and Laravel route map for: %s', $title),
            'graphql-api' => sprintf('Create a GraphQL-vs-REST API contract decision for: %s', $title),
            'graph-traversal' => sprintf('Create a BFS/DFS traversal plan with API and database guardrails for: %s', $title),
            'javascript-closures' => sprintf('Create a JavaScript closure explanation, example, and interview checklist for: %s', $title),
            'react-render-performance' => sprintf('Create a React render optimization plan for: %s', $title),
            'sql-injection-defense' => sprintf('Create a SQL Injection prevention and parameterized-query plan for: %s', $title),
            'csrf-protection' => sprintf('Create a CSRF token, SameSite cookie, and state-changing request defense plan for: %s', $title),
            'xss-defense' => sprintf('Create an XSS output-escaping and safe-rendering plan for: %s', $title),
            'idor-access-control' => sprintf('Create an IDOR object-level authorization review for: %s', $title),
            'security-misconfiguration' => sprintf('Create a security misconfiguration readiness checklist for: %s', $title),
            'auth-security' => sprintf('Create a policy-backed authorization example for: %s', $title),
            'database-eloquent' => str_contains(Str::lower($title), 'covering index')
                ? sprintf('Create a covering-index and EXPLAIN verification plan for: %s', $title)
                : sprintf('Create a migration/model/query example for: %s', $title),
            'files-media' => sprintf('Create a validated storage lifecycle example for: %s', $title),
            'async-workflow' => sprintf('Create a queued job or event/listener example for: %s', $title),
            'performance-cache' => sprintf('Create a cache or query-performance example for: %s', $title),
            'container-architecture' => sprintf('Create a container binding and dependency-injection example for: %s', $title),
            'realtime-events' => sprintf('Create an event and broadcast planning example for: %s', $title),
            'system-design-tradeoffs' => sprintf('Create a System Design tradeoff plan for: %s', $title),
            'reverse-proxy-edge' => sprintf('Create a reverse-proxy failure-mode and blast-radius plan for: %s', $title),
            'siem-elk-observability' => sprintf('Create a SIEM and ELK security logging plan for: %s', $title),
            'load-balancing' => sprintf('Create a load-balancer algorithm and failure-mode plan for: %s', $title),
            'kubernetes-orchestration' => sprintf('Create a Kubernetes object map and rollout plan for: %s', $title),
            'jwt-token-storage' => sprintf('Create a JWT token-storage threat model for: %s', $title),
            'jwt-revocation' => sprintf('Create a JWT revocation architecture plan for: %s', $title),
            'oauth-flow' => sprintf('Create an OAuth flow, client type, token boundary, and verification plan for: %s', $title),
            'lsm-tree-storage' => sprintf('Create an LSM Tree read/write path plan for: %s', $title),
            'llm-foundations' => sprintf('Create an LLM, Predictive AI, Generative AI, and feedback model explanation for: %s', $title),
            'rag-systems' => sprintf('Create a RAG, Long Context, CAG, or hybrid chatbot context strategy decision for: %s', $title),
            'ai-cloud-interview' => sprintf('Create an AI-assisted Cloud interview rubric and verification workflow for: %s', $title),
            'blade-ui' => sprintf('Create a Blade view/component example for: %s', $title),
            'testing-quality' => sprintf('Write a failing-first test that proves: %s', $title),
            'docker-runtime' => sprintf('Add or verify a runtime smoke check for: %s', $title),
            'ai-workflow' => sprintf('Write an implementation prompt and review checklist for: %s', $title),
            'interview' => sprintf('Turn this interview answer into a tiny code-backed example: %s', $title),
            default => sprintf('Build a thin Laravel route/controller/service slice for: %s', $title),
        };
    }

    /**
     * Normalize requested mapping count.
     */
    private function normalizeLimit(int|string|null $limit): int
    {
        $value = is_numeric($limit) ? (int) $limit : 12;

        return max(1, min(50, $value));
    }
}
