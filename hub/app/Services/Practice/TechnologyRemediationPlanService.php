<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyRemediationPlanService
{
    /**
     * Create remediation plans from scored technology assessments.
     */
    public function __construct(
        private readonly TechnologySkillAssessmentService $assessments,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a remediation plan for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $assessment = $this->assessments->build($technology, $filters);
        $tasks = collect($assessment['rubric'])
            ->map(fn (array $row, int $index): array => $this->taskFromRubric($technology, $row, $index + 1))
            ->values()
            ->all();

        return [
            'title' => sprintf('Technology Remediation Plan: %s', $technology),
            'technology' => $technology,
            'assessment' => $assessment,
            'tasks' => $tasks,
            'commands' => $this->commandsFor($technology),
            'next_routes' => [
                'implementation_lab' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'interview_pack' => sprintf('/practice/technology-interview-pack/%s', $technology),
                'skill_assessment' => sprintf('/practice/technology-skill-assessment/%s', $technology),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $tasks,
                fn (array $task): string => $task['label'],
            ),
        ];
    }

    /**
     * Convert one rubric criterion into a concrete remediation task.
     *
     * @return array{step: int, label: string, problem: string, action: string, evidence: string, focus_file: string}
     */
    private function taskFromRubric(string $technology, array $row, int $step): array
    {
        return [
            'step' => $step,
            'label' => sprintf('Repair %s', $row['criterion']),
            'problem' => $row['pass_signal'],
            'action' => $this->actionFor($technology, (string) $row['criterion']),
            'evidence' => $row['evidence'],
            'focus_file' => $this->focusFileFor((string) $row['criterion']),
        ];
    }

    /**
     * Build a remediation action for one assessment criterion.
     */
    private function actionFor(string $technology, string $criterion): string
    {
        if ($technology === 'react-render-performance') {
            return match ($criterion) {
                'Profiler baseline' => 'Capture a before/after React Profiler note with commit duration and rendered component count.',
                'Memoization fit' => 'Rewrite the recommendation as one chosen tool and one rejected alternative with the reason.',
                'Structural render control' => 'Check whether state locality, context splitting, pagination, or virtualization should happen before memoization.',
                'Dependency safety' => 'Review every useMemo and useCallback dependency array for stale values and unnecessary dependencies.',
                default => 'Practice a two-minute React performance answer that names the profiler signal, chosen fix, tradeoff, and evidence.',
            };
        }

        if ($technology === 'javascript-closures') {
            return match ($criterion) {
                'Lexical this model' => 'Rewrite the answer around where the arrow function is created and which surrounding scope supplies `this`.',
                'Call-site comparison' => 'Trace one object normal method and one object arrow property, then explain why only the normal method receives call-site `this`.',
                'Binding traps' => 'Add examples showing that obj.arrow(), call(), apply(), and bind() do not rebind arrow-function `this`.',
                'Practical callback use' => 'Add one timer, event handler, array callback, or class callback where an arrow intentionally keeps outer `this`.',
                'Lexical scope model' => 'Rewrite the answer around one outer function, one inner function, and the exact variables captured from lexical scope.',
                'Captured binding behavior' => 'Trace createCounter() across repeated calls and explain why count is a binding that remains reachable.',
                'Practical usage' => 'Add examples for private state, event handlers, callbacks, debounce, throttle, memoization, or React hook closures.',
                'Interview traps' => 'Add a var versus let loop example and one stale-closure example from async code, timers, or hooks.',
                default => 'Practice a two-minute JavaScript closure answer that names lexical scope, captured binding, practical use, traps, and evidence.',
            };
        }

        if ($technology === 'php') {
            return match ($criterion) {
                'Call-frame model' => 'Rewrite the answer around one function call: parameters enter, local variables exist while the frame is active, return value leaves, and the frame unwinds.',
                'Heap-backed data' => 'Add a PHP example with a large array or object graph and explain why references can keep that data alive beyond one call frame.',
                'Cleanup and references' => 'Use unset(), scope exit, or memory_get_usage() to explain cleanup while avoiding claims about manual heap allocation.',
                'Failure modes' => 'Add a note for deep recursion, large arrays, circular references, retained references, or long-running worker stale state.',
                default => 'Practice a two-minute PHP runtime answer that separates stack frames, heap-backed data, references, cleanup, and production memory risks.',
            };
        }

        if ($technology === 'sql-injection-defense') {
            return match ($criterion) {
                'Injection mechanism' => 'Rewrite the explanation so it says user input changed SQL structure, then show the safe data-only version.',
                'Parameterized values' => 'Replace one concatenated SQL value with query builder or raw SQL bindings.',
                'Identifier allowlists' => 'Add an explicit allowlist for dynamic sort, column, or table choices before building the query.',
                'Attack-payload tests' => 'Add tests for OR 1=1, quoted comments, UNION-like probes, and invalid identifier input.',
                default => 'Practice a two-minute SQL Injection answer that names the input boundary, binding, allowlist, payload tests, and evidence.',
            };
        }

        if ($technology === 'csrf-protection') {
            return match ($criterion) {
                'Attack mechanism' => 'Rewrite the explanation so it says the browser sends cookies automatically while the user did not intend the state change.',
                'Token proof' => 'Add or inspect the `@csrf` field, XSRF header, or Sanctum CSRF-cookie bootstrap for the selected flow.',
                'Cookie boundary' => 'Document SameSite, Secure, Domain, and Path behavior without treating SameSite as a token replacement.',
                'Failure tests' => 'Add tests for missing token, stale token, and unsafe GET mutation behavior.',
                default => 'Practice a two-minute CSRF answer that names browser cookies, request intent, token proof, SameSite, failure tests, and evidence.',
            };
        }

        if ($technology === 'xss-defense') {
            return match ($criterion) {
                'Attack variants' => 'Rewrite the explanation so it separates reflected, stored, and DOM-based XSS with one concrete browser execution path.',
                'Context-aware escaping' => 'Replace one unsafe output with escaped Blade output, safe JSON serialization, or the right context-specific encoder.',
                'Raw HTML trust boundary' => 'Remove one `{!! !!}` path or document the sanitizer and trust boundary that makes it acceptable.',
                'Payload rendering tests' => 'Add tests for script tags, event-handler attributes, javascript: URLs, or unsafe DOM sinks rendering harmlessly.',
                default => 'Practice a two-minute XSS answer that names variant, output context, escaped rendering, sanitizer boundary, payload tests, and CSP limits.',
            };
        }

        if ($technology === 'auth-security') {
            return match ($criterion) {
                'Lifecycle model' => 'Rewrite the answer around the full authentication lifecycle: login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                'Session and reset controls' => 'Add or inspect throttling, session regeneration, reset-token expiry, logout invalidation, token rotation, token revocation, and remember-me boundaries.',
                'Failure-path tests' => 'Add tests for brute force, stale reset token, reused reset token, old session reuse, logged-out session reuse, and revoked token reuse.',
                'Sensitive auth logging' => 'Add suspicious-login logging fields without storing passwords, reset tokens, session IDs, bearer tokens, or full sensitive request payloads.',
                default => 'Practice a two-minute Broken Authentication answer that separates identity proof, session lifecycle, reset safety, token revocation, logging, and authorization boundaries.',
            };
        }

        if ($technology === 'security-misconfiguration') {
            return match ($criterion) {
                'Unsafe default detection' => 'Audit APP_DEBUG, APP_ENV, exception rendering, default credentials, exposed .env files, public storage, and secret leakage before release.',
                'Environment drift control' => 'Write expected local, CI, staging, and production values with an owner for every setting that changes by environment.',
                'Boundary hardening' => 'Review CORS allowlists, security headers, HTTPS enforcement, session cookie flags, trusted proxies, and storage visibility as concrete exposure boundaries.',
                'Deployment smoke checks' => 'Add a fail-closed smoke check that blocks release when debug mode, exposed secrets, missing headers, broad CORS, or proxy drift is detected.',
                default => 'Practice a two-minute Security Misconfiguration answer that names unsafe defaults, environment drift, boundary hardening, smoke checks, and evidence.',
            };
        }

        if ($technology === 'idor-access-control') {
            return match ($criterion) {
                'Object surface inventory' => 'List every route, API action, download, export, nested resource, route parameter, owner, tenant, role, and allowed object action.',
                'Scoped lookup' => 'Replace one direct model lookup with a query constrained through the current user, tenant, organization, team, or parent relationship before returning the object.',
                'Object authorization' => 'Add or inspect a Policy, Gate, FormRequest authorize(), or service check that authorizes the exact object and requested action.',
                'Cross-user denial tests' => 'Add tests where user A replays user B identifiers across read, update, delete, download, export, and nested-resource flows.',
                default => 'Practice a two-minute IDOR answer that names broken object-level authorization, scoped lookup, policy checks, denial tests, status-code choice, and evidence.',
            };
        }

        if ($technology === 'oauth-flow') {
            return match ($criterion) {
                'Flow fit' => 'Classify the client, secret-storage ability, user delegation need, and machine-to-machine requirement before choosing the OAuth flow.',
                'PKCE proof' => 'Add the verifier lifecycle: generate high entropy code_verifier, derive S256 code_challenge, send only challenge, exchange code with verifier, then clear it.',
                'Callback validation' => 'Add failure cases for state mismatch, unexpected redirect URI, reused authorization code, and token fields arriving in the browser callback URL.',
                'Token boundary' => 'Document where access tokens, ID tokens, refresh tokens, scopes, audience, lifetime, rotation, and logs are allowed or forbidden.',
                default => 'Practice a two-minute OAuth answer that names client type, chosen flow, PKCE proof, callback validation, token boundary, and evidence.',
            };
        }

        if ($technology === 'graph-traversal') {
            return match ($criterion) {
                'Traversal goal' => 'Rewrite the answer around the goal first: nearest result, shortest unweighted path, branch exploration, dependency reasoning, or subtree validation.',
                'State model' => 'Add one example that shows BFS queue frontier order and one example that shows DFS stack or recursion path order.',
                'Cycle safety' => 'Add a visited-set check plus tests for cyclic graphs, shared dependencies, max depth, and max nodes.',
                'System fit' => 'Map the traversal choice to one API crawling case and one database hierarchy case with batching, pagination, rate limits, fan-out, timeout, and memory notes.',
                default => 'Practice a two-minute BFS versus DFS answer that names traversal goal, queue versus stack, shortest-path behavior, guardrails, and evidence.',
            };
        }

        if ($technology === 'llm-foundations') {
            return match ($criterion) {
                'Output contract' => 'Rewrite the comparison around output contracts: score, label, forecast, and ranking for Predictive AI; generated text, code, image, summary, and answer for Generative AI.',
                'Input evidence' => 'Add one input path for each side: historical data and labels for prediction; prompt, context, retrieval evidence, and constraints for generation.',
                'Evaluation fit' => 'Split the quality plan into predictive metrics and generative checks instead of using one generic accuracy or hallucination checklist.',
                'Failure modes' => 'Name separate controls for drift, overfitting, biased labels, hallucination, fabricated citations, prompt injection, and unsafe code.',
                default => 'Practice a two-minute AI comparison answer that names product fit, output contract, input evidence, metrics, failure modes, and verification.',
            };
        }

        if ($technology === 'rag-systems') {
            return match ($criterion) {
                'Context strategy fit' => 'Rewrite the plan so it chooses RAG, Long Context, CAG, or hybrid routing from corpus freshness, size, stability, permissions, latency, and cost before naming a retrieval pattern.',
                'Answer contract' => 'Add selected_context_path, rag_pattern, token_budget, cache_version, source_version, citations, fallback_reason, and missing_evidence to the chatbot response contract.',
                'Router guardrails' => 'Add router rules that scope retrieval filters, cache keys, packed documents, and citations by user, tenant, permission, locale, and content version.',
                'Failure and evaluation tests' => 'Add tests for stale cache, unauthorized chunks, missing citations, oversized context, low-confidence retrieval, and hybrid route selection.',
                default => 'Practice a two-minute chatbot context strategy answer that separates RAG, Long Context, CAG, hybrid routing, answer contract, guardrails, and evidence.',
            };
        }

        if ($technology === 'database-eloquent') {
            return match ($criterion) {
                'Protected invariant' => 'Rewrite the remediation note so the protected invariant is explicit before the locking approach is chosen.',
                'Transaction boundary' => 'Move the critical read, validation, and write into one `DB::transaction()` and apply `lockForUpdate()` before protected state is checked.',
                'Lock scope and ordering' => 'Review the lock query for selective indexed lookup, short critical section, no external calls, and consistent ordering when multiple rows are locked.',
                'Failure and operations evidence' => 'Add concurrent-request, insufficient-state, deadlock retry, lock timeout, hot-row contention, and lock-wait monitoring evidence.',
                'Plan baseline' => 'Capture EXPLAIN (ANALYZE, BUFFERS) before and after the index change, then record Index Scan, Index Only Scan, Heap Fetches, and buffer reads.',
                'INCLUDE design' => 'Rewrite the index proposal so filter and order columns are index keys while only returned hot-query columns are listed in INCLUDE.',
                'Visibility health' => 'Add a note for visibility map state, VACUUM or autovacuum expectations, and why stale visibility data can keep heap access alive.',
                'Cost guardrails' => 'Estimate index size, write overhead, bloat risk, rollback command, and the condition that would make this covering index not worth shipping.',
                default => 'Practice a two-minute covering-index answer that names heap fetch cost, INCLUDE design, visibility-map caveat, EXPLAIN evidence, and rollback.',
            };
        }

        return match ($criterion) {
            'Source understanding' => sprintf('Reopen the `%s` source records and write a one-paragraph reason why each record maps to this technology.', $technology),
            'Laravel layer placement' => 'Trace the route, controller, request, service, and test files; move misplaced behavior into the correct layer.',
            'Implementation evidence' => 'Open the changed files list and add or adjust the smallest code change that proves implementation happened in Laravel code.',
            'Verification discipline' => 'Run each verification command and record what behavior the command proves.',
            default => 'Practice a two-minute explanation that names the source record, changed files, commands, and tradeoffs.',
        };
    }

    /**
     * Choose the likely file area for one remediation criterion.
     */
    private function focusFileFor(string $criterion): string
    {
        return match ($criterion) {
            'Call-frame model' => 'PHP function example and runtime-memory interview notes',
            'Heap-backed data' => 'PHP array, object graph, reference, and memory-pressure example',
            'Cleanup and references' => 'unset(), scope exit, reference counting, GC, and memory_get_usage notes',
            'Failure modes' => 'recursion, large arrays, circular references, retained references, and worker-state notes',
            'Injection mechanism' => 'SQL query construction and interview answer notes',
            'Parameterized values' => 'query builder, Eloquent where clauses, or DB::select bindings',
            'Identifier allowlists' => 'request validation and sort/filter allowlist map',
            'Attack-payload tests' => 'tests/Feature security payload cases',
            'Attack mechanism' => 'browser request flow and threat-model notes',
            'Token proof' => 'Blade forms, XSRF headers, Sanctum CSRF bootstrap, and middleware verification',
            'Cookie boundary' => 'config/session.php, cookie flags, SameSite, Secure, Domain, and Path settings',
            'Failure tests' => 'tests/Feature CSRF token and 419 behavior cases',
            'Attack variants' => 'Blade views, API JSON handoff, and browser DOM update notes',
            'Context-aware escaping' => 'Blade `{{ }}`, attributes, URLs, and JavaScript serialization',
            'Raw HTML trust boundary' => 'sanitizer service, trusted HTML policy, and `{!! !!}` review',
            'Payload rendering tests' => 'tests/Feature XSS payload rendering cases',
            'Lifecycle model' => 'authentication lifecycle notes across login, session, remember-me, password reset, MFA recovery, logout, refresh, and revocation',
            'Session and reset controls' => 'app/Http/Requests/Auth, auth services, config/session.php, password reset broker, token rotation, and remember-me settings',
            'Failure-path tests' => 'tests/Feature/Auth broken-authentication lifecycle, reset-token, session reuse, and token revocation cases',
            'Sensitive auth logging' => 'authentication audit logging, suspicious-login events, redaction policy, and log context fields',
            'Unsafe default detection' => 'config/app.php, .env.example, exception rendering, storage visibility, and secret exposure notes',
            'Environment drift control' => 'local, CI, staging, and production configuration matrix',
            'Boundary hardening' => 'CORS, security headers, HTTPS, cookie flags, trusted proxies, and public storage settings',
            'Deployment smoke checks' => 'configuration readiness tests, release gates, rollback notes, and owner checklist',
            'Object surface inventory' => 'routes/api/**, routes/web/**, controllers, route parameters, nested resources, downloads, and exports',
            'Scoped lookup' => 'Eloquent relationships, tenant scopes, owner constraints, route model binding, and repository queries',
            'Object authorization' => 'Policy, Gate, FormRequest authorize(), controller authorize(), and domain authorization service',
            'Cross-user denial tests' => 'tests/Feature IDOR cross-user, cross-tenant, nested-resource, download, and export cases',
            'Profiler baseline' => 'React Profiler capture and target component file',
            'Memoization fit' => 'component props, derived values, and callback definitions',
            'Structural render control' => 'state owner, context provider, list rendering, and virtualization boundary',
            'Dependency safety' => 'useMemo/useCallback dependency arrays',
            'Lexical this model' => 'resources/js/arrow-this-comparison.js and lexical-this explanation notes',
            'Call-site comparison' => 'normal method, arrow property, object call, and call-site output trace',
            'Binding traps' => 'obj.arrow(), call(), apply(), bind(), and interview trap notes',
            'Practical callback use' => 'timer, event handler, array callback, or class callback arrow-this example',
            'Lexical scope model' => 'resources/js/closure-counter.js and lexical-scope explanation notes',
            'Captured binding behavior' => 'createCounter(), private state, and repeated-call output trace',
            'Practical usage' => 'event handlers, callbacks, debounce, throttle, memoization, and React hook closure examples',
            'Interview traps' => 'var versus let loop callback and stale-closure notes',
            'Flow fit' => 'OAuth client classification and flow selection notes',
            'PKCE proof' => 'authorize URL builder, verifier storage, S256 challenge derivation, and token exchange',
            'Callback validation' => 'callback controller/request validation and OAuth failure-path tests',
            'Token boundary' => 'token exchange service, storage decision, refresh rotation, and log redaction notes',
            'Traversal goal' => 'BFS versus DFS decision note and selected graph fixture',
            'State model' => 'queue frontier, stack path, recursion depth, and traversal-order tests',
            'Cycle safety' => 'visited set, cyclic graph fixture, max-depth, and max-node tests',
            'System fit' => 'API crawling, database hierarchy, batching, pagination, rate-limit, fan-out, timeout, and memory notes',
            'Output contract' => 'AI type comparison table and product output notes',
            'Input evidence' => 'historical data, labels, prompt, context, retrieval evidence, and constraints',
            'Evaluation fit' => 'predictive metrics and generative quality-check plan',
            'Failure modes' => 'drift, overfitting, biased labels, hallucination, prompt injection, and unsafe-code notes',
            'Context strategy fit' => 'chatbot context routing table for RAG, Long Context, CAG, and hybrid decisions',
            'Answer contract' => 'chatbot answer API contract, citations, source version, cache version, fallback reason, and missing evidence',
            'Router guardrails' => 'app/Services/Rag/ChatbotContextRouter.php, tenant-scoped retrieval filters, cache keys, packed documents, and citation policy',
            'Failure and evaluation tests' => 'tests/Feature RAG context strategy, cache invalidation, permission filtering, token budget, citation, fallback, and hybrid routing cases',
            'Protected invariant' => 'database locking invariant note for inventory, balance, booking, or workflow-state writes',
            'Transaction boundary' => 'DB::transaction(), lockForUpdate(), protected read, validation, and write service code',
            'Lock scope and ordering' => 'selective indexed lock query, short critical section, consistent multi-row lock ordering, and no external calls inside the transaction',
            'Failure and operations evidence' => 'concurrent-request tests, deadlock retry, lock timeout, hot-row contention, and lock-wait monitoring notes',
            'Plan baseline' => 'EXPLAIN (ANALYZE, BUFFERS), Heap Fetches, buffer reads, and query-plan notes',
            'INCLUDE design' => 'PostgreSQL CREATE INDEX key columns and INCLUDE column review',
            'Visibility health' => 'visibility map, VACUUM, autovacuum, and heap-access caveats',
            'Cost guardrails' => 'index size, bloat risk, write overhead, rollback, and migration notes',
            'Source understanding' => 'data/**/*.json and record workspace source panel',
            'Laravel layer placement' => 'routes/**, app/Http/Controllers/**, app/Services/**',
            'Implementation evidence' => 'generated snippet files and changed-file list',
            'Verification discipline' => 'tests/** and route-list output',
            default => 'portfolio artifact and interview pack',
        };
    }

    /**
     * Return commands for remediation verification.
     *
     * @return array<int, string>
     */
    private function commandsFor(string $technology): array
    {
        return [
            sprintf('php artisan test --filter TechnologySkillAssessmentTest --filter=%s', $technology),
            sprintf('php artisan route:list --path=technology-%s', str_replace('_', '-', $technology)),
            'vendor\\bin\\pint --test',
        ];
    }
}
