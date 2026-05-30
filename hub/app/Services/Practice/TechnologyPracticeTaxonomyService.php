<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyPracticeTaxonomyService
{
    /**
     * Create a taxonomy from the shared content-practice services.
     */
    public function __construct(
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
        private readonly ContentPracticeStarterSnippetService $snippets,
    ) {}

    /**
     * Build the supported technology catalog for UI and API discovery.
     *
     * @param  array{search?: string|null, strength?: string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $search = $this->normalizeSearch($filters['search'] ?? null);
        $strength = $this->normalizeStrength($filters['strength'] ?? null);
        $allItems = collect($this->mapper->technologies())
            ->map(fn (string $technology): array => $this->toItem($technology, $search))
            ->map(fn (array $item): array => $this->withSearchMatch($item, $search));

        $searchMatchedItems = $allItems
            ->filter(fn (array $item): bool => $this->matchesSearch($item, $search));

        $items = $searchMatchedItems
            ->filter(fn (array $item): bool => $this->matchesStrength($item, $strength))
            ->sortByDesc(fn (array $item): int => $item['match_score'])
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                'filters' => [
                    'search' => $search,
                    'strength' => $strength,
                ],
                'suggested_searches' => $this->suggestedSearches(),
                'strength_counts' => $this->strengthCountsFor($searchMatchedItems->all()),
                'filtered' => $search !== null,
                'has_results' => $items !== [],
                'empty_state' => $this->emptyStateFor($search, $items),
                'technology_count' => count($items),
                'total_technology_count' => $allItems->count(),
                'workbench_count' => collect($items)->filter(fn (array $item): bool => $item['related_workbench'] !== null)->count(),
                'starter_file_count' => collect($items)->sum(fn (array $item): int => count($item['starter_files'])),
                'alias_count' => collect($items)->sum(fn (array $item): int => count($item['aliases'])),
            ],
        ];
    }

    /**
     * Normalize the user-provided taxonomy search term.
     */
    private function normalizeSearch(?string $search): ?string
    {
        $value = trim((string) $search);

        return $value === '' ? null : Str::lower($value);
    }

    /**
     * Normalize the optional match-strength filter.
     */
    private function normalizeStrength(?string $strength): ?string
    {
        $value = Str::lower(trim((string) $strength));

        return in_array($value, ['strong', 'direct', 'partial'], true) ? $value : null;
    }

    /**
     * Determine whether a taxonomy item matches the current search term.
     *
     * @param  array<string, mixed>  $item
     */
    private function matchesSearch(array $item, ?string $search): bool
    {
        return $search === null || $item['matched_by'] !== [];
    }

    /**
     * Determine whether a taxonomy item matches the optional strength filter.
     *
     * @param  array<string, mixed>  $item
     */
    private function matchesStrength(array $item, ?string $strength): bool
    {
        return $strength === null || $item['match_strength'] === $strength;
    }

    /**
     * Count available match-strength buckets before a strength filter is applied.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{strong: int, direct: int, partial: int}
     */
    private function strengthCountsFor(array $items): array
    {
        $counts = collect($items)
            ->pluck('match_strength')
            ->filter()
            ->countBy();

        return [
            'strong' => (int) ($counts['strong'] ?? 0),
            'direct' => (int) ($counts['direct'] ?? 0),
            'partial' => (int) ($counts['partial'] ?? 0),
        ];
    }

    /**
     * Attach search match reasons so filtered results explain themselves.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function withSearchMatch(array $item, ?string $search): array
    {
        $item['matched_by'] = $search === null ? [] : $this->matchDetailsFor($item, $search);
        $item['match_score'] = $this->matchScoreFor($item['matched_by']);
        $item['match_strength'] = $this->matchStrengthFor($item['match_score'], $search);

        return $item;
    }

    /**
     * Return the fields that matched the search term.
     *
     * @param  array<string, mixed>  $item
     * @return array<int, array{field: string, value: string}>
     */
    private function matchDetailsFor(array $item, string $search): array
    {
        $candidates = [
            'technology' => [(string) $item['technology']],
            'learning_goal' => [(string) $item['learning_goal']],
            'practice' => [(string) ($item['practice']['title'] ?? '')],
            'workbench' => [
                (string) ($item['related_workbench']['label'] ?? ''),
                (string) ($item['related_workbench']['concept'] ?? ''),
            ],
            'alias' => $item['aliases'],
            'concept' => $item['concepts'],
            'starter_file' => $item['starter_files'],
        ];

        return collect($candidates)
            ->flatMap(fn (array $values, string $field): array => collect($values)
                ->filter(fn (string $value): bool => str_contains(Str::lower($value), $search))
                ->map(fn (string $value): array => [
                    'field' => $field,
                    'value' => $value,
                ])
                ->values()
                ->all())
            ->values()
            ->all();
    }

    /**
     * Score match quality so exact technology and alias hits appear first.
     *
     * @param  array<int, array{field: string, value: string}>  $matches
     */
    private function matchScoreFor(array $matches): int
    {
        return collect($matches)->sum(fn (array $match): int => match ($match['field']) {
            'technology' => 80,
            'alias' => 60,
            'concept' => 45,
            'workbench' => 35,
            'practice' => 25,
            'learning_goal' => 15,
            'starter_file' => 10,
            default => 1,
        });
    }

    /**
     * Convert a numeric match score into a learner-facing relevance label.
     */
    private function matchStrengthFor(int $score, ?string $search): ?string
    {
        if ($search === null) {
            return null;
        }

        return match (true) {
            $score >= 100 => 'strong',
            $score >= 45 => 'direct',
            default => 'partial',
        };
    }

    /**
     * Return high-signal searches that demonstrate the taxonomy aliases.
     *
     * @return array<int, string>
     */
    private function suggestedSearches(): array
    {
        return [
            'ppo',
            'predictive ai',
            'generative ai',
            'javascript closure',
            'security misconfiguration',
            'covering index',
            'bfs dfs',
            'breadth first search',
            'depth first search',
            'heap fetch',
            'explain analyze',
            'vacuum analyze',
            'database locking',
            'lockforupdate',
            'restful api naming',
            'endpoint naming',
            'nested resource',
            'graphql',
            'rag',
            'agent memory',
            'reverse proxy',
            'agi',
            'jwt',
            'csrf',
            'xss',
            'idor',
            'kubernetes',
            'lsm',
            'oauth',
            'hallucination',
            'load balancer',
            'stack heap',
        ];
    }

    /**
     * Return a user-facing empty-state payload for unmatched searches.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{title: string, body: string, suggestions: array<int, string>}|null
     */
    private function emptyStateFor(?string $search, array $items): ?array
    {
        if ($search === null || $items !== []) {
            return null;
        }

        return [
            'title' => "No technology matches '{$search}'.",
            'body' => 'Try a shorter keyword, a technology acronym, or one of the suggested searches below.',
            'suggestions' => $this->suggestedSearches(),
        ];
    }

    /**
     * Convert one technology key into a reusable taxonomy item.
     *
     * @return array<string, mixed>
     */
    private function toItem(string $technology, ?string $search = null): array
    {
        $snippets = $this->snippets->snippetsFor($this->snippetSeedFor($technology, $search));
        $metadata = $this->metadataFor($technology);

        return [
            'technology' => $technology,
            'aliases' => $metadata['aliases'],
            'concepts' => $metadata['concepts'],
            'learning_goal' => $metadata['learning_goal'],
            'matched_by' => [],
            'match_score' => 0,
            'match_strength' => null,
            'practice' => $this->mapper->practiceSummaryFor($technology),
            'related_workbench' => $this->workbenchFor($technology, $search),
            'starter_files' => collect($snippets)
                ->pluck('file')
                ->values()
                ->all(),
            'starter_count' => count($snippets),
            'links' => [
                'board' => $this->taxonomyPath("/practice/technology-board?technology={$technology}", $search),
                'pipeline' => $this->taxonomyPath("/practice/technology-learning-pipeline/{$technology}", $search),
                'code_examples' => $this->taxonomyPath("/practice/technology-code-examples/{$technology}", $search),
            ],
        ];
    }

    /**
     * Build the record shape used to choose starter files for taxonomy cards.
     *
     * @return array<string, mixed>
     */
    private function snippetSeedFor(string $technology, ?string $search = null): array
    {
        $seed = ['technology' => $technology];

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord([
            'technology' => $technology,
            'content' => [
                'title' => $search ?? '',
                'body' => $search ?? '',
            ],
            'task' => $search ?? '',
        ])) {
            return [
                ...$seed,
                'content' => [
                    'title' => 'Database locking and lockForUpdate starter',
                    'body' => 'Use transactions, row locks, deadlock awareness, lock contention checks, and short critical sections.',
                ],
                'task' => 'Create a database locking plan for inventory, balance, or workflow-state updates.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesRecord([
            'technology' => $technology,
            'content' => [
                'title' => $search ?? '',
                'body' => $search ?? '',
            ],
            'task' => $search ?? '',
        ])) {
            return [
                ...$seed,
                'content' => [
                    'title' => 'Covering Index query-plan starter',
                    'body' => 'Use EXPLAIN, Index Only Scan, Heap Fetches, visibility map, VACUUM, and INCLUDE columns.',
                ],
                'task' => 'Create a covering-index and EXPLAIN verification plan.',
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($search ?? '')) {
            return [
                ...$seed,
                'content' => [
                    'title' => '4 core AI agent memory types that help it work like a developer',
                    'body' => 'Working memory, episodic memory, semantic memory, and procedural memory need separate contracts.',
                ],
                'task' => 'Create an AI agent memory plan with scope, freshness, confidence, permission, retention, and correction guardrails.',
            ];
        }

        return $seed;
    }

    /**
     * Return the best workbench link for a technology and optional taxonomy search.
     *
     * @return array{label: string, path: string, route_name: string|null, concept: string}|null
     */
    private function workbenchFor(string $technology, ?string $search): ?array
    {
        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($search ?? '')) {
            return $this->workbenches->agentMemoryLink();
        }

        return $this->workbenches->linkFor($technology);
    }

    /**
     * Preserve taxonomy search context when linking into downstream workflows.
     */
    private function taxonomyPath(string $path, ?string $search): string
    {
        if ($search === null) {
            return $path;
        }

        $separator = str_contains($path, '?') ? '&' : '?';

        return $path.$separator.http_build_query(['search' => $search]);
    }

    /**
     * Return learner-facing labels that make each technology key discoverable.
     *
     * @return array{aliases: array<int, string>, concepts: array<int, string>, learning_goal: string}
     */
    private function metadataFor(string $technology): array
    {
        return match ($technology) {
            'php' => [
                'aliases' => ['PHP', 'stack memory', 'heap memory', 'stack heap', 'stack vs heap', 'call stack', 'call frame', 'memory allocation', 'garbage collection', 'reference counting'],
                'concepts' => ['function call frame', 'local variables', 'object lifetime', 'array memory pressure', 'recursion depth', 'long-running worker memory'],
                'learning_goal' => 'Explain PHP runtime behavior through call frames, stack versus heap lifetime, references, garbage collection, and memory pressure in long-running backend code.',
            ],
            'llm-foundations' => [
                'aliases' => ['LLM', 'large language model', 'Markov decision process', 'Predictive AI', 'Generative AI', 'AI agent memory', 'agent memory', 'working memory', 'episodic memory', 'semantic memory', 'procedural memory', 'forecasting model', 'classification model', 'MDP', 'RLHF', 'PPO', 'reward model', 'attention', 'AGI'],
                'concepts' => ['prediction score', 'classification label', 'generated content', 'state', 'policy', 'action', 'transition', 'reward', 'tool feedback', 'task state', 'session history', 'durable project knowledge', 'workflow playbook'],
                'learning_goal' => 'Explain AI behavior by separating predictive outputs, generative outputs, agent memory types, probability, repeated decisions, attention, reward, PPO, tools, feedback, and guarded AGI claims.',
            ],
            'rag-systems' => [
                'aliases' => ['RAG', 'retrieval augmented generation', 'classic RAG', 'Graph RAG', 'Agentic RAG', 'Long Context', 'CAG', 'Cache-Augmented Generation', 'context strategy', 'grounded answer', 'citation coverage'],
                'concepts' => ['chunk retrieval', 'source IDs', 'entity graph', 'edge provenance', 'tool loop', 'long context packing', 'context cache', 'groundedness evaluation'],
                'learning_goal' => 'Choose RAG, Long Context, CAG, classic RAG, Graph RAG, or Agentic RAG from knowledge shape, relationships, tool needs, freshness, risk, cost, permissions, and evidence requirements.',
            ],
            'system-design-tradeoffs' => [
                'aliases' => ['System Design', 'tradeoff', 'notification system', 'WebSocket', 'Long Polling', 'Kafka', 'team maturity'],
                'concepts' => ['clarifying questions', 'latency requirement', 'failure impact', 'operational complexity', 'consistency cost', 'level framing'],
                'learning_goal' => 'Answer System Design interviews by exposing constraints, costs, rejected alternatives, and team-fit tradeoffs before choosing technology.',
            ],
            'ai-cloud-interview' => [
                'aliases' => ['AI interview', 'Cloud Engineer AI', 'Terraform AI review', 'CloudFormation AI review', 'AWS documentation', 'CLAUDE.md', 'system prompt', 'thought partner'],
                'concepts' => ['rubric scoring', 'verification workflow', 'IaC review', 'AI failure story', 'source-of-truth check', 'team guardrails'],
                'learning_goal' => 'Interview Cloud Engineers on practical AI usage by requiring concrete tasks, prompt workflow, IaC verification, failure stories, and team-level guardrails.',
            ],
            'graphql-api' => [
                'aliases' => ['GraphQL', 'REST', 'schema', 'resolver', 'query', 'mutation', 'overfetching', 'underfetching'],
                'concepts' => ['schema contract', 'client-selected fields', 'REST resources', 'resolver boundary', 'N+1 risk', 'cache semantics', 'API style decision'],
                'learning_goal' => 'Choose REST or GraphQL by client data shape, HTTP caching needs, schema governance, resolver performance, authorization boundaries, and operational cost.',
            ],
            'restful-api-naming' => [
                'aliases' => ['RESTful API naming', 'endpoint naming', 'resource nouns', 'plural resources', 'nested resources', 'query parameters', 'route names'],
                'concepts' => ['resource contract', 'HTTP verb semantics', 'collection endpoint', 'single-resource endpoint', 'domain action endpoint', 'Laravel route naming'],
                'learning_goal' => 'Design RESTful API paths that use resource nouns, stable route names, clear query parameters, shallow nesting, explicit business actions, and reviewable Laravel route maps.',
            ],
            'graph-traversal' => [
                'aliases' => ['BFS', 'DFS', 'BFS DFS', 'Breadth-First Search', 'Depth-First Search', 'breadth first search', 'depth first search', 'graph traversal', 'tree traversal', 'queue traversal', 'stack traversal', 'visited set', 'shortest path'],
                'concepts' => ['level-order traversal', 'depth-first traversal', 'cycle detection', 'API crawling', 'database hierarchy', 'dependency graph', 'memory frontier', 'recursion depth'],
                'learning_goal' => 'Choose BFS or DFS from traversal goal, graph shape, shortest-path needs, memory budget, cycle risk, API rate limits, database hierarchy depth, and verification evidence.',
            ],
            'javascript-closures' => [
                'aliases' => ['JavaScript closure', 'closure', 'JavaScript hoisting', 'hoisting', 'arrow function', 'arrow this', 'lexical this', 'call-site this', 'lexical scope', 'function factory', 'private variable', 'debounce', 'throttle', 'stale closure', 'temporal dead zone'],
                'concepts' => ['captured binding', 'inner function state', 'callback scope', 'event handler state', 'arrow function lexical this', 'normal function dynamic this', 'object method this trap', 'var versus let loop behavior', 'function declaration hoisting', 'temporal dead zone', 'React hook dependency closure'],
                'learning_goal' => 'Explain JavaScript closures, hoisting, and arrow-function `this` through lexical scope, captured bindings, function declarations, var versus let/const behavior, async callbacks, object method traps, and interview stale-closure traps.',
            ],
            'react-render-performance' => [
                'aliases' => ['React render', 'React re-render', 'React.memo', 'memo', 'useMemo', 'useCallback', 'React Profiler', 'prop churn'],
                'concepts' => ['profiler evidence', 'stable props', 'callback identity', 'derived value memoization', 'state locality', 'context splitting', 'list virtualization'],
                'learning_goal' => 'Optimize React rendering by measuring first, then choosing React.memo, useMemo, useCallback, state placement, or virtualization for the actual render cause.',
            ],
            'sql-injection-defense' => [
                'aliases' => ['SQL Injection', 'parameterized query', 'prepared statement', 'query binding', 'DB bindings', 'OR 1=1'],
                'concepts' => ['SQL structure versus values', 'bindings', 'raw SQL review', 'identifier allowlist', 'malicious payload tests'],
                'learning_goal' => 'Explain SQL Injection clearly and prevent it with parameterized queries, Laravel query builder bindings, allowlisted identifiers, and targeted security tests.',
            ],
            'csrf-protection' => [
                'aliases' => ['CSRF', 'Cross-Site Request Forgery', '@csrf', 'SameSite', 'XSRF', '419 Page Expired', 'Sanctum CSRF cookie'],
                'concepts' => ['browser cookies', 'state-changing request', 'CSRF token', 'SameSite cookie', 'safe HTTP methods', 'stale-token tests'],
                'learning_goal' => 'Explain CSRF as unwanted browser state change and prevent it with Laravel CSRF tokens, SameSite cookies, method boundaries, Sanctum cookie flow, and focused 419 tests.',
            ],
            'xss-defense' => [
                'aliases' => ['XSS', 'Cross-Site Scripting', 'reflected XSS', 'stored XSS', 'DOM XSS', 'Blade escaping', 'raw HTML', 'Content Security Policy', 'CSP'],
                'concepts' => ['output context', 'escaped Blade output', 'safe JSON serialization', 'rich-text sanitization', 'unsafe DOM sink', 'payload tests'],
                'learning_goal' => 'Explain XSS as untrusted data reaching executable browser contexts and prevent it with context-aware escaping, sanitized rich text, safe JavaScript data handoff, CSP, and payload tests.',
            ],
            'idor-access-control' => [
                'aliases' => ['IDOR', 'Insecure Direct Object Reference', 'object-level authorization', 'BOLA', 'tenant scoping', 'ownership check'],
                'concepts' => ['object ID swapping', 'policy check', 'scoped query', 'tenant boundary', 'two-user feature test', '403 versus 404 decision'],
                'learning_goal' => 'Explain IDOR as object-level authorization failure and prevent it with Laravel policies, scoped queries, tenant ownership checks, audit logs, and two-user tests.',
            ],
            'security-misconfiguration' => [
                'aliases' => ['Security Misconfiguration', 'misconfiguration', 'APP_DEBUG', 'debug mode', '.env leak', 'exposed secrets', 'security headers', 'CORS', 'trusted proxies', 'public storage bucket'],
                'concepts' => ['unsafe defaults', 'production config drift', 'secret exposure', 'debug output', 'header hardening', 'deployment smoke check'],
                'learning_goal' => 'Explain Security Misconfiguration as unsafe runtime setup and prevent it with secure defaults, environment checks, secret scanning, header hardening, CORS review, proxy boundaries, and deployment verification.',
            ],
            'database-eloquent' => [
                'aliases' => ['Database', 'Eloquent', 'query builder', 'migration', 'covering index', 'Index Only Scan', 'heap fetch', 'heap fetches', 'INCLUDE columns', 'included columns', 'visibility map', 'VACUUM', 'VACUUM ANALYZE', 'autovacuum', 'EXPLAIN', 'EXPLAIN ANALYZE', 'EXPLAIN ANALYZE BUFFERS', 'BUFFERS', 'database locking', 'lockForUpdate', 'row lock', 'table lock', 'deadlock', 'lock contention', 'race condition', 'concurrency control'],
                'concepts' => ['schema design', 'query plan', 'composite index', 'included columns', 'heap fetch cost', 'random IO', 'visibility-map health', 'autovacuum health', 'index bloat', 'write overhead', 'write amplification', 'rollback migration', 'transaction boundary', 'row-level lock', 'deadlock retry', 'hot row contention'],
                'learning_goal' => 'Design Laravel data access with schema discipline, Eloquent boundaries, query-plan reading, covering-index tradeoffs, database locking, transaction boundaries, deadlock awareness, Heap Fetches evidence, visibility-map checks, VACUUM expectations, index bloat review, and production-safe migration habits.',
            ],
            'lsm-tree-storage' => [
                'aliases' => ['LSM Tree', 'SSTable', 'memtable', 'WAL', 'compaction', 'Bloom Filter'],
                'concepts' => ['write path', 'read amplification', 'write amplification', 'tombstones', 'range scans'],
                'learning_goal' => 'Explain NoSQL write speed through storage-engine layout and operational tradeoffs.',
            ],
            'oauth-flow' => [
                'aliases' => ['OAuth', 'OAuth2', 'Implicit Flow', 'PKCE', 'Proof Key for Code Exchange', 'Authorization Code with PKCE', 'Authorization Code', 'Client Credentials', 'Machine-to-machine', 'Service-to-service', 'OIDC'],
                'concepts' => ['front-channel token risk', 'state validation', 'code verifier', 'S256 code challenge', 'public client', 'client authentication', 'service scopes', 'audience validation', 'refresh rotation'],
                'learning_goal' => 'Choose a safer OAuth flow for the client type: use Authorization Code with PKCE for public clients, migrate legacy browser clients away from token-bearing callbacks, and use Client Credentials for service-to-service access.',
            ],
            'jwt-token-storage' => [
                'aliases' => ['JWT storage', 'localStorage', 'HttpOnly cookie', 'bearer token', 'refresh token'],
                'concepts' => ['XSS', 'CSRF', 'SameSite', 'refresh rotation', 'client threat model'],
                'learning_goal' => 'Choose token storage from client type, XSS/CSRF exposure, token lifetime, and logout needs.',
            ],
            'jwt-revocation' => [
                'aliases' => ['JWT revocation', 'denylist', 'jti', 'token version', 'refresh family'],
                'concepts' => ['server-side state', 'middleware lookup', 'pruning', 'store outage', 'reuse detection'],
                'learning_goal' => 'Design how a JWT can stop working before expiry without pretending stateless tokens revoke themselves.',
            ],
            'kubernetes-orchestration' => [
                'aliases' => ['Kubernetes', 'K8s', 'pod', 'deployment', 'service', 'ingress', 'control plane'],
                'concepts' => ['desired state', 'rollout', 'readiness', 'liveness', 'worker node'],
                'learning_goal' => 'Map runtime responsibilities to Kubernetes objects and explain rollout behavior without vocabulary-only answers.',
            ],
            'reverse-proxy-edge' => [
                'aliases' => ['reverse proxy', 'Cloudflare', 'edge proxy', 'core proxy', 'HTTP 500', 'feature file', 'origin server', 'blast radius'],
                'concepts' => ['shared request path', 'origin reachability', 'config validation', 'staged rollout', 'health gate', 'fail-small design'],
                'learning_goal' => 'Explain why healthy origins can be unreachable when the reverse proxy or edge layer fails, and design blast-radius controls for proxy changes.',
            ],
            'siem-elk-observability' => [
                'aliases' => ['SIEM', 'SIEM ELK', 'ELK Stack', 'Elasticsearch', 'Logstash', 'Kibana', 'Beats', 'Elastic Agent', 'security information and event management'],
                'concepts' => ['log shipping', 'parsing', 'field normalization', 'correlation rules', 'alert ownership', 'retention', 'privacy controls'],
                'learning_goal' => 'Design a SIEM and ELK pipeline that turns raw logs into searchable evidence, high-signal detections, dashboards, runbooks, and controlled retention.',
            ],
            'load-balancing' => [
                'aliases' => ['load balancer', 'round robin', 'weighted round robin', 'least connections', 'IP hash'],
                'concepts' => ['health checks', 'upstream capacity', 'session affinity', 'failover'],
                'learning_goal' => 'Choose a load-balancing algorithm from traffic shape, capacity, sessions, and failure modes.',
            ],
            'ai-workflow' => [
                'aliases' => ['AI workflow', 'prompting', 'hallucination', 'AI review', 'guardrail'],
                'concepts' => ['evidence map', 'verification command', 'scope control', 'human review'],
                'learning_goal' => 'Use AI for coding while keeping evidence, runtime checks, and human review as the authority.',
            ],
            default => [
                'aliases' => [$technology],
                'concepts' => [$technology],
                'learning_goal' => "Practice {$technology} with a concrete workbench, starter files, and verification commands.",
            ],
        };
    }
}
