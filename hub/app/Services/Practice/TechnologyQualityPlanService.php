<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyQualityPlanService
{
    /**
     * Create quality plans for technology learning pipelines.
     */
    public function __construct(
        private readonly TechnologyPipelineIndexService $pipelines,
        private readonly PracticeQualityGateService $qualityGate,
    ) {}

    /**
     * Build a quality plan for every filtered technology pipeline.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $pipelines = $this->pipelines->build($filters);

        $items = collect($pipelines['items'])
            ->map(fn (array $item): array => $this->toQualityItem($item))
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                ...$pipelines['meta'],
                'quality_plan_count' => count($items),
                'minimum_status' => $this->minimumStatus($items),
            ],
        ];
    }

    /**
     * Convert one pipeline card into a quality plan card.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function toQualityItem(array $item): array
    {
        $baseline = $this->baselineFor($item);
        $gate = $this->qualityGate->evaluate($baseline);

        return [
            'technology' => $item['technology'],
            'record_count' => $item['record_count'],
            'source_count' => $item['source_count'],
            'practice' => $item['practice'],
            'related_workbench' => $item['related_workbench'],
            'pipeline_route' => $item['pipeline_route'],
            'api_pipeline_route' => $item['api_pipeline_route'],
            'quality_gate' => $gate,
            'baseline' => $baseline,
            'commands' => [
                'php artisan test --filter Technology'.str($item['technology'])->studly()->replace('-', '')->toString().'Test',
                'php artisan test --filter RouteFileDocumentationTest',
                'vendor\\bin\\pint --test',
            ],
            'acceptance_checks' => $this->acceptanceChecksFor((string) $item['technology'], $item),
            'evidence_checks' => $this->evidenceChecksFor((string) $item['technology'], $item),
            'risk_note' => $this->riskNoteFor((string) $item['technology'], $item),
        ];
    }

    /**
     * Build recommended verification counts for one technology.
     *
     * @param  array<string, mixed>  $item
     * @return array{tests: int, assertions: int, failures: int, pint: bool}
     */
    private function baselineFor(array $item): array
    {
        $tests = max(2, min(12, (int) ceil(((int) $item['record_count']) / 4)));

        return [
            'tests' => $tests,
            'assertions' => max($tests * 4, (int) $item['source_count'] * 3),
            'failures' => 0,
            'pint' => true,
        ];
    }

    /**
     * Return acceptance checks tailored to one technology family.
     *
     * @return array<int, string>
     */
    private function acceptanceChecksFor(string $technology, array $item = []): array
    {
        if ($technology === 'javascript-closures' && $this->isArrowThisItem($item)) {
            return [
                'The answer states that arrow functions do not create their own `this`.',
                'The example contrasts arrow lexical `this` with normal function call-site `this`.',
                '`obj.arrow()`, `call`, `apply`, and `bind` traps are explained without claiming the arrow can be rebound.',
                'Practical guidance names where arrow callbacks help and where normal methods or constructors are safer.',
            ];
        }

        if ($technology === 'llm-foundations' && $this->isAgentMemoryItem($item)) {
            return [
                'The plan separates working, episodic, semantic, and procedural memory before storage or retrieval decisions.',
                'Reusable memory items include source, freshness, confidence, permission, retention, and correction metadata.',
                'Stale semantic memory, private episodic memory, procedural mismatch, and working-memory leakage have explicit rejection checks.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesPipelineItem($item)) {
            return [
                'Predictive AI outputs are described as scores, labels, forecasts, rankings, or risk decisions, not generated content.',
                'Generative AI outputs are described as new text, image, code, summary, answer, or multimodal content, not a calibrated prediction by default.',
                'The answer uses separate evaluation language for prediction metrics and generation quality checks.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($item)) {
            return [
                'The invariant names the race condition being protected, such as stock cannot go below zero or balance cannot be spent twice.',
                '`DB::transaction()` wraps the read, validation, and write steps that must behave as one atomic critical section.',
                '`lockForUpdate()` is applied inside the transaction on a selective, indexed row lookup before the protected value is checked or changed.',
                'External API calls, slow work, and user interaction are kept outside the lock so contention windows stay short.',
                'Deadlock, timeout, retry, and monitoring behavior are documented before the change is considered production-ready.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesPipelineItem($item)) {
            return [
                'Before and after EXPLAIN (ANALYZE, BUFFERS) output proves Heap Fetches dropped for the hot query.',
                'The covering index uses key columns for filter/order and INCLUDE only for projected columns needed by the query.',
                'The rollout notes cover visibility map health, VACUUM or autovacuum, index size, write overhead, bloat risk, and rollback.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationItem($item)) {
            return [
                'The review maps login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation as one authentication lifecycle.',
                'Session regeneration, login throttling, reset-token expiry, logout invalidation, and token revocation are represented as explicit controls.',
                'Failure-path tests prove brute force, stale reset token, reused reset token, old session reuse, logged-out session reuse, and revoked token reuse fail closed.',
                'The answer separates authentication identity proof from authorization permission checks.',
            ];
        }

        return match ($technology) {
            'api-validation' => [
                'Request validation rejects malformed payloads.',
                'Controller returns a stable JSON response shape.',
                'Feature tests cover success and validation failure.',
            ],
            'graphql-api' => [
                'The decision names why REST endpoints or GraphQL schema/query shape fits the client workflow.',
                'Authorization is checked at every endpoint, resource, field, mutation, or resolver boundary that can expose data.',
                'N+1 resolver risk, caching semantics, error shape, and query-cost limits are documented before implementation.',
            ],
            'graph-traversal' => [
                'The answer chooses BFS or DFS from the traversal goal instead of claiming one is always faster.',
                'BFS examples use a queue for level-order, nearest result, or shortest unweighted path behavior.',
                'DFS examples use stack or recursion for branch exploration, dependency reasoning, subtree validation, or backtracking.',
                'API and database examples include visited set, cycle detection, max depth, fan-out limits, batching, pagination, rate limits, and memory tradeoffs.',
            ],
            'javascript-closures' => [
                'The answer defines closure as a function plus access to variables from its lexical scope.',
                'The example proves captured variable bindings through repeated calls such as createCounter().',
                'Interview checks cover var versus let loops, stale closures, private state, debounce, throttle, or hook dependencies.',
            ],
            'react-render-performance' => [
                'React Profiler evidence exists before memoization is added.',
                'React.memo, useMemo, and useCallback are used for their correct responsibility, not applied everywhere.',
                'Large-list, context, and state-placement problems are handled structurally before relying on memo.',
            ],
            'sql-injection-defense' => [
                'User-controlled values are passed through bindings or query builder parameters.',
                'Dynamic identifiers such as columns, tables, and sort direction use allowlists.',
                'Feature tests include malicious payloads and prove payloads remain data, not SQL logic.',
            ],
            'csrf-protection' => [
                'State-changing browser routes require CSRF token validation or an explicit non-cookie auth reason.',
                'SameSite cookie behavior is documented separately from CSRF token verification.',
                'Feature tests cover missing token, stale token, and unsafe GET mutation behavior.',
            ],
            'xss-defense' => [
                'Untrusted output is rendered with context-aware escaping for HTML, attributes, JavaScript, and URLs.',
                'Raw HTML paths have an explicit trust boundary, sanitizer, or removal plan.',
                'Feature tests cover reflected, stored, and DOM-style payloads proving attacker input renders as data.',
            ],
            'idor-access-control' => [
                'Every object route maps to owner, tenant, team, role, or public access rules before implementation.',
                'Backend code scopes the query before returning the model and checks policy or Gate on the exact object.',
                'Feature tests prove another user or tenant cannot read, update, delete, download, export, or access nested child objects by changing IDs.',
                'The response decision documents 403 versus 404 based on object-existence sensitivity.',
            ],
            'security-misconfiguration' => [
                'Production runtime flags such as APP_DEBUG, APP_ENV, logging, cache, and config cache are checked before deploy.',
                'Secrets, public storage, CORS, security headers, cookie flags, and trusted proxy settings have explicit safe defaults.',
                'Deployment smoke checks prove the app fails closed when configuration is missing, exposed, or overly permissive.',
            ],
            'auth-security' => [
                'Authorization is enforced outside Blade-only checks.',
                'Sensitive data is excluded from responses and logs.',
                'Tests cover allowed and denied access paths.',
            ],
            'database-eloquent' => [
                'Queries select only the fields needed by the screen.',
                'List views avoid obvious N+1 query paths.',
                'Tests cover filtering and empty result behavior.',
            ],
            'performance-cache' => [
                'Cache keys include the filters that change output.',
                'Invalidation or freshness expectations are documented.',
                'Tests prove uncached and cached responses stay equivalent.',
            ],
            'load-balancing' => [
                'Algorithm choice is justified by traffic shape and upstream capacity.',
                'Health-check and failover behavior is covered by at least one scenario.',
                'Sticky-session or forwarded-header risk is named when relevant.',
            ],
            'system-design-tradeoffs' => [
                'The answer asks clarifying questions before choosing Kafka, WebSocket, Redis, or any other technology.',
                'The chosen architecture states its cost and why the rejected option costs more in this context.',
                'The plan checks who pays if the design is wrong, where complexity lives, and whether the team can operate it.',
            ],
            'reverse-proxy-edge' => [
                'The request path names proxy, edge module, load balancer, and origin responsibilities.',
                'Config files and generated feature data have schema, size, staged rollout, and rollback controls.',
                'Blast radius and health gates are defined before the proxy layer accepts all traffic.',
            ],
            'siem-elk-observability' => [
                'The plan distinguishes SIEM operating capability from ELK component names.',
                'Log sources, normalized fields, parsing, retention, privacy, and alert ownership are all explicit.',
                'Detection rules include evidence queries and runbooks instead of dashboard-only monitoring.',
            ],
            'kubernetes-orchestration' => [
                'Object responsibilities are separated across deployment, service, ingress, and probe concerns.',
                'Rollout and rollback behavior is stated before capacity claims.',
                'Stateful workload caveats are documented instead of hidden behind replicas.',
            ],
            'jwt-token-storage' => [
                'Storage choice names XSS, CSRF, token lifetime, and client type tradeoffs.',
                'Refresh rotation or logout behavior is part of the acceptance criteria.',
                'The plan blocks localStorage promotion for sensitive browser tokens unless risk is explicit.',
            ],
            'jwt-revocation' => [
                'Middleware verifies signature and expiry before revocation lookup.',
                'Store outage behavior and token pruning are covered.',
                'Tests prove a revoked or stale token stops working before expiry.',
            ],
            'oauth-flow' => [
                'Implicit Flow callbacks are blocked or migrated to Authorization Code with PKCE when a user-facing public client is involved.',
                'Client Credentials is used only for confidential machine-to-machine clients, never browser or mobile public clients.',
                'The code verifier is generated per login attempt, stored only for the pending authorization request, and cleared after token exchange.',
                'State, S256 code challenge, redirect URI, audience, scope, and token-leakage validation are represented in tests.',
            ],
            'lsm-tree-storage' => [
                'Write path covers WAL, memtable, flush, SSTable, and compaction.',
                'Read path covers Bloom filters, tombstones, and read amplification.',
                'The workload assumption states whether write throughput or read latency is the priority.',
            ],
            'llm-foundations' => [
                'The explanation separates next-token probability from the full decision loop.',
                'Attention, reward model, PPO, and environment feedback are each named with a concrete role.',
                'Claims about reasoning or AGI are guarded by uncertainty and verification language.',
            ],
            'rag-systems' => [
                'The plan names whether RAG, Long Context, CAG, or hybrid routing fits the chatbot context strategy.',
                'The RAG pattern decision still explains why classic RAG, Graph RAG, or Agentic RAG fits the actual knowledge shape.',
                'Retrieval output keeps source IDs, citations, freshness, and groundedness checks separate from generated text.',
                'Long Context uses token budgets and source markers; CAG uses permission-aware cache keys and stale-cache invalidation.',
                'Graph edges, agent tool calls, and hybrid router decisions are added only with provenance, iteration limits, and failure-mode tests.',
            ],
            'ai-cloud-interview' => [
                'The answer includes one recent AI-assisted Cloud task with prompt shape, output edits, and result.',
                'Terraform, CloudFormation, IAM, Kubernetes, and AWS Documentation claims require source verification or dry-run evidence.',
                'The candidate can name one AI failure story and turn it into a reusable team guardrail.',
            ],
            'testing-quality' => [
                'Behavior tests assert output, not implementation details.',
                'Pint passes before refactor work is considered complete.',
                'Failure messages point to the next concrete fix.',
            ],
            'php' => [
                'PHP examples prove behavior with a small function or class and an observable output.',
                'Runtime-memory topics distinguish call frames, heap-backed data, references, and cleanup.',
                'Long-running worker or large-array examples include a memory-risk note before optimization claims.',
            ],
            default => [
                'A feature test covers the primary happy path.',
                'At least one edge case or failure path is covered.',
                'Pint and focused tests pass before moving to portfolio work.',
            ],
        };
    }

    /**
     * Return evidence that proves the quality plan is not checklist-only.
     *
     * @return array<int, string>
     */
    private function evidenceChecksFor(string $technology, array $item = []): array
    {
        if ($technology === 'javascript-closures' && $this->isArrowThisItem($item)) {
            return [
                'A JavaScript snippet compares `obj.normal()` with `obj.arrow()` and records the different output.',
                'A callback example shows an arrow preserving outer `this` inside a timer, event handler, array callback, or class method.',
                'A trap note proves `call`, `apply`, and `bind` cannot rebind arrow-function `this`.',
            ];
        }

        if ($technology === 'llm-foundations' && $this->isAgentMemoryItem($item)) {
            return [
                'A memory contract table names working, episodic, semantic, and procedural memory with allowed data and retrieval rules.',
                'A governance checklist proves source, freshness, confidence, permission, retention, and correction metadata are present before reuse.',
                'Failure tests or review notes block stale semantic facts, private session notes, mismatched playbooks, and temporary working-memory promotion.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesPipelineItem($item)) {
            return [
                'A comparison table or test fixture separates output contract, input data, evaluation metric, and failure mode for both AI types.',
                'Predictive examples include precision, recall, calibration, AUC, error rate, or business-lift evidence.',
                'Generative examples include groundedness, citation quality, safety, test result, human review, or prompt-injection evidence.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($item)) {
            return [
                'A concurrency fixture describes two simultaneous requests and the exact row, account, order, or workflow state they compete for.',
                'A code example or test shows `lockForUpdate()` inside `DB::transaction()` before decrementing stock, spending balance, or changing state.',
                'Failure evidence proves insufficient stock, stale state, deadlock retry, or timeout behavior fails closed without double writes.',
                'Operational notes identify lock wait metrics, hot-row risk, indexed lookup requirements, and the smallest safe transaction scope.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesPipelineItem($item)) {
            return [
                'A query-plan fixture or captured EXPLAIN output records Index Scan versus Index Only Scan and Heap Fetches.',
                'A migration or SQL note shows CREATE INDEX key columns plus INCLUDE columns without adding unused payload data.',
                'Operational evidence names VACUUM or autovacuum expectations and the rollback command for removing the index.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationItem($item)) {
            return [
                'A lifecycle checklist names each identity boundary and the Laravel file responsible for the control.',
                'Feature tests or notes prove throttling, session regeneration, reset-token expiry, logout invalidation, and revocation behavior.',
                'Security logs include suspicious-login context without passwords, reset tokens, session IDs, or bearer tokens.',
            ];
        }

        return match ($technology) {
            'oauth-flow' => [
                'A captured authorize URL contains code_challenge and code_challenge_method=S256, but never code_verifier.',
                'Token exchange logs or tests prove missing, wrong, expired, or reused code_verifier values are rejected.',
                'Callback handling rejects access_token, id_token, token_type, or expires_in in URL input for browser clients.',
            ],
            'jwt-token-storage' => [
                'A storage decision table names client type, token lifetime, XSS exposure, CSRF exposure, and refresh strategy.',
                'A failure test proves sensitive browser tokens are not promoted into localStorage by default.',
            ],
            'graph-traversal' => [
                'A traversal fixture proves BFS order with a queue and DFS order with a stack or recursion.',
                'A cycle test proves visited-set behavior prevents repeated work or infinite loops.',
                'A system note maps BFS or DFS to one API crawling case and one database hierarchy case with depth, fan-out, pagination, and memory limits.',
            ],
            'xss-defense' => [
                'Rendered HTML evidence shows attacker-controlled tags and attributes display as data or are sanitized.',
                'DOM update paths use textContent, safe URL validation, or a sanitizer with a documented policy.',
            ],
            'rag-systems' => [
                'A context strategy plan records whether the chatbot used retrieval, context packing, cached context, or hybrid routing.',
                'A fixture proves source IDs, citations, freshness, permission scope, and missing-evidence behavior remain visible in the answer contract.',
                'Long Context examples include token-budget and lost-in-the-middle checks; CAG examples include cache version and permission-key checks.',
            ],
            'idor-access-control' => [
                'A route-list excerpt identifies object ID, nested resource, download, export, batch, and signed URL surfaces.',
                'A failing-first or replay note shows the exact ID swap request the fix is meant to block.',
                'Passing two-user or two-tenant tests prove denial for read, update, delete, download, export, and nested routes.',
                'Monitoring evidence names denied object access log fields without storing raw tokens or sensitive payloads.',
            ],
            'security-misconfiguration' => [
                'A readiness checklist marks debug mode, environment name, secret exposure, storage visibility, headers, CORS, proxies, and cookie flags.',
                'A smoke test or config audit rejects APP_DEBUG=true, exposed .env files, permissive CORS, missing security headers, or trusted-proxy drift in production.',
                'Release evidence includes rollback notes and the owner responsible for each environment-specific setting.',
            ],
            'sql-injection-defense' => [
                'A query log or test double proves request values are bound parameters rather than concatenated SQL.',
                'Sort columns, table names, and directions are selected from allowlists before query construction.',
            ],
            'javascript-closures' => [
                'A small JavaScript snippet shows an outer variable staying reachable after the outer function returns.',
                'A loop example or checklist explains shared var binding versus block-scoped let binding.',
                'A stale-closure note names async callback, timer, event handler, debounce, throttle, memoization, or React hook dependency risk.',
            ],
            'php' => [
                'A small code sample marks local call-frame state versus heap-pressure data.',
                'The explanation names why PHP code normally manages stack and heap indirectly.',
                'A failure-mode note covers recursion depth, large arrays, retained references, or worker state.',
            ],
            default => [
                'A focused test demonstrates the primary behavior from the user or API boundary.',
                'At least one failure-path assertion proves the guardrail rejects bad input or unsafe state.',
            ],
        };
    }

    /**
     * Return the main risk to watch while practicing one technology.
     */
    private function riskNoteFor(string $technology, array $item = []): string
    {
        if ($technology === 'javascript-closures' && $this->isArrowThisItem($item)) {
            return 'Arrow-function `this` answers are weak when they say arrow syntax is just shorter, but cannot explain lexical `this`, normal function call-site `this`, obj.arrow() traps, or call/apply/bind limitations.';
        }

        if ($technology === 'llm-foundations' && $this->isAgentMemoryItem($item)) {
            return 'AI agent memory work is risky when one prompt-history bucket silently mixes temporary task state, private session notes, stale repo facts, and unreviewed playbooks.';
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesPipelineItem($item)) {
            return 'Predictive AI versus Generative AI explanations become weak when they reuse one evaluation checklist and ignore that prediction errors, drift, hallucination, and unsafe generated code need different controls.';
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($item)) {
            return 'Database locking work is risky when teams add `lockForUpdate()` without a transaction boundary, indexed lookup, short critical section, deadlock plan, or proof that concurrent requests cannot double-spend or oversell.';
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesPipelineItem($item)) {
            return 'Covering Index work is risky when an index is added without proving heap fetch reduction, checking visibility-map health, or accounting for index bloat and write overhead.';
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationItem($item)) {
            return 'Broken Authentication work is incomplete when teams test only successful login and ignore session fixation, brute force, stale reset tokens, logout invalidation, token revocation, and sensitive auth logging.';
        }

        return match ($technology) {
            'api-validation' => 'Validation can drift from the documented API shape when controllers start normalizing input manually.',
            'graphql-api' => 'GraphQL can look flexible while hiding resolver N+1 queries, authorization gaps, cache complexity, and unbounded query cost.',
            'graph-traversal' => 'BFS and DFS answers are weak when they describe traversal order but ignore the actual goal, cycles, depth limits, fan-out, pagination, rate limits, and memory pressure.',
            'javascript-closures' => 'Closure answers are weak when they say "inner function remembers variables" but cannot trace lexical scope, captured bindings, stale closures, or var versus let behavior.',
            'react-render-performance' => 'React performance work is risky when memoization is added without profiler evidence or correct dependency review.',
            'sql-injection-defense' => 'SQL Injection prevention is incomplete when teams rely on escaping strings but still concatenate request input or unreviewed identifiers.',
            'csrf-protection' => 'CSRF protection is incomplete when teams rely on SameSite alone or allow GET routes to mutate cookie-authenticated state.',
            'xss-defense' => 'XSS prevention is incomplete when teams rely on validation alone or render raw HTML without context-aware escaping, sanitization, and payload tests.',
            'idor-access-control' => 'IDOR prevention is incomplete when teams check only authentication, roles, hidden UI, or random IDs but do not prove object-level authorization with scoped queries, policies, and two-user or two-tenant denial tests.',
            'security-misconfiguration' => 'Security Misconfiguration work is incomplete when teams fix code vulnerabilities but deploy with debug mode, exposed secrets, weak headers, broad CORS, public storage, or incorrect proxy trust.',
            'auth-security' => 'Security practice is incomplete if authorization is only represented as UI visibility.',
            'database-eloquent' => 'Data practice can look correct on small JSON samples while hiding inefficient query boundaries.',
            'performance-cache' => 'Cache examples are risky when freshness rules are not stated next to the implementation.',
            'load-balancing' => 'Load-balancer answers are weak when they name algorithms without health checks, session state, or upstream capacity.',
            'system-design-tradeoffs' => 'System Design answers are weak when they are technically correct but hide the tradeoffs, business impact, and team capacity assumptions.',
            'reverse-proxy-edge' => 'Reverse-proxy answers are weak when they assume healthy origins matter if the shared edge request path is broken.',
            'siem-elk-observability' => 'SIEM answers are weak when they stop at collecting logs and ignore parsing quality, alert noise, retention cost, privacy, and runbook ownership.',
            'kubernetes-orchestration' => 'Kubernetes practice can become vocabulary-only unless object ownership and rollout behavior are tested.',
            'jwt-token-storage' => 'Token-storage recommendations are unsafe when they ignore client type, XSS exposure, or refresh-token lifetime.',
            'jwt-revocation' => 'JWT revocation plans fail in production when revocation-store outages and pruning are not designed.',
            'oauth-flow' => 'OAuth work is risky when PKCE is described as optional ceremony instead of proof that the authorization code belongs to the client that started the login.',
            'lsm-tree-storage' => 'LSM Tree explanations are incomplete when they only praise writes and ignore read/space amplification.',
            'llm-foundations' => 'LLM explanations become misleading when they say "just next-token prediction" or promise AGI without explaining reward and feedback limits.',
            'rag-systems' => 'RAG, Long Context, and CAG systems fail quietly when teams improve prompts while retrieval quality, source markers, cache freshness, citations, permissions, and fallback behavior remain untested.',
            'ai-cloud-interview' => 'AI interview answers are weak when they praise tools without concrete tasks, failure stories, and source-of-truth verification.',
            'testing-quality' => 'Test-count targets are only useful when assertions protect learner-visible behavior.',
            'php' => 'PHP runtime answers are weak when they describe stack and heap as manual allocation instead of a mental model for calls, references, GC, and memory pressure.',
            default => 'Keep the quality gate tied to observable behavior so the exercise remains practical instead of checklist-only.',
        };
    }

    /**
     * Summarize the lowest quality status across all plan items.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private function minimumStatus(array $items): string
    {
        return collect($items)
            ->contains(fn (array $item): bool => $item['quality_gate']['passed'] === false)
                ? 'needs-work'
                : 'ready';
    }

    /**
     * Detect arrow-function `this` work inside the broader JavaScript closure lane.
     */
    private function isArrowThisItem(array $item): bool
    {
        $haystack = strtolower((string) json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect broken-authentication work inside the broader auth-security lane.
     */
    private function isBrokenAuthenticationItem(array $item): bool
    {
        $haystack = strtolower((string) json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }

    /**
     * Detect agent-memory work inside the broader LLM foundations lane.
     */
    private function isAgentMemoryItem(array $item): bool
    {
        $haystack = strtolower((string) json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return AiAgentMemoryTopicService::matchesText($haystack)
            || (($item['related_workbench']['path'] ?? null) === '/workbench/ai-agent-memory-plan');
    }
}
