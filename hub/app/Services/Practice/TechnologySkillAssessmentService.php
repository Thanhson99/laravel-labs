<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologySkillAssessmentService
{
    /**
     * Create skill assessments from technology interview packs.
     */
    public function __construct(
        private readonly TechnologyInterviewPackService $interviewPacks,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a scored self-assessment for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $pack = $this->interviewPacks->build($technology, $filters);
        $rubric = $this->rubricFor($technology, $pack, $filters);

        return [
            'title' => sprintf('Technology Skill Assessment: %s', $technology),
            'technology' => $technology,
            'interview_pack' => $pack,
            'rubric' => $rubric,
            'max_score' => 100,
            'pass_score' => 80,
            'readiness' => [
                'ready_signal' => 'You can explain the source record, changed files, test evidence, and Laravel layer placement without reading the page.',
                'repeat_signal' => 'Repeat the implementation lab when you cannot cite concrete files, commands, or design tradeoffs.',
            ],
            'improvement_tasks' => $this->improvementTasks($technology, $pack, $filters),
            'progress_payload' => $this->progressPayload->fromRows(
                $rubric,
                fn (array $row): string => $row['criterion'],
            ),
        ];
    }

    /**
     * Build a 100-point rubric from the interview pack.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function rubricFor(string $technology, array $pack, array $filters = []): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesPack($pack, $filters)) {
            return $this->phpRuntimeMemoryRubric($pack);
        }

        if ($technology === 'react-render-performance') {
            return $this->reactRenderRubric($pack);
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisPack($pack)) {
            return $this->javascriptArrowThisRubric($pack);
        }

        if ($technology === 'javascript-closures') {
            return $this->javascriptClosureRubric($pack);
        }

        if ($technology === 'sql-injection-defense') {
            return $this->sqlInjectionRubric($pack);
        }

        if ($technology === 'csrf-protection') {
            return $this->csrfProtectionRubric($pack);
        }

        if ($technology === 'xss-defense') {
            return $this->xssDefenseRubric($pack);
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationPack($pack)) {
            return $this->brokenAuthenticationRubric($pack);
        }

        if ($technology === 'security-misconfiguration') {
            return $this->securityMisconfigurationRubric($pack);
        }

        if ($technology === 'idor-access-control') {
            return $this->idorAccessControlRubric($pack);
        }

        if ($technology === 'oauth-flow') {
            return $this->oauthFlowRubric($pack);
        }

        if ($technology === 'graph-traversal') {
            return $this->graphTraversalRubric($pack);
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesPack($pack, $filters)) {
            return $this->aiTypeComparisonRubric($pack);
        }

        if ($technology === 'rag-systems') {
            return $this->ragContextStrategyRubric($pack);
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesPack($pack, $filters)) {
            return $this->coveringIndexRubric($pack);
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($pack, $filters)) {
            return $this->databaseLockingRubric($pack);
        }

        return [
            [
                'criterion' => 'Source understanding',
                'points' => 20,
                'evidence' => $pack['evidence_to_cite'][4] ?? 'Source records are listed in the portfolio artifact.',
                'pass_signal' => sprintf('Explains why the selected JSON records map to `%s`.', $technology),
            ],
            [
                'criterion' => 'Laravel layer placement',
                'points' => 25,
                'evidence' => 'Answer outline explains route, controller, service, and test responsibilities.',
                'pass_signal' => 'Can point to the layer that owns validation, orchestration, business behavior, and verification.',
            ],
            [
                'criterion' => 'Implementation evidence',
                'points' => 20,
                'evidence' => $pack['evidence_to_cite'][2] ?? 'Changed files are listed.',
                'pass_signal' => 'Changed files match the selected technology and avoid content-only fake progress.',
            ],
            [
                'criterion' => 'Verification discipline',
                'points' => 20,
                'evidence' => $pack['evidence_to_cite'][3] ?? 'Verification commands are listed.',
                'pass_signal' => 'Can rerun focused tests and explain what each command proves.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise oral summary with source, implementation, and evidence.',
            ],
        ];
    }

    /**
     * Build next improvement tasks for weak assessment areas.
     *
     * @return array<int, string>
     */
    private function improvementTasks(string $technology, array $pack = [], array $filters = []): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesPack($pack, $filters)) {
            return [
                'Write a PHP function example and mark which values belong to the active call frame.',
                'Create a large array or object graph example and explain why it creates heap pressure.',
                'Use unset(), scope exit, or memory_get_usage() to explain cleanup without claiming manual heap allocation.',
                'Name one production failure mode: deep recursion, large arrays, retained references, or long-running worker stale state.',
                'Practice a two-minute answer that separates stack frames, heap-backed data, references, cleanup, and PHP caveats.',
            ];
        }

        if ($technology === 'react-render-performance') {
            return [
                'Capture one React Profiler before/after note for the selected component.',
                'Explain why React.memo, useMemo, useCallback, state locality, or virtualization is the smallest valid fix.',
                'Review dependency arrays and stale-closure risk for every memoized value or callback.',
                'Remove one unnecessary memoization from a cheap component and explain why it was noise.',
                'Practice a two-minute answer that separates measurement, chosen tool, tradeoff, and evidence.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisPack($pack)) {
            return [
                'Write one object with a normal method and an arrow property, then trace why `obj.normal()` and `obj.arrow()` produce different `this` values.',
                'Create one callback example where an arrow intentionally keeps outer `this`.',
                'Add one trap note proving `call`, `apply`, and `bind` cannot rebind arrow-function `this`.',
                'Rewrite one arrow object method back to normal method syntax and explain why that is safer.',
                'Practice a two-minute answer that separates lexical `this`, call-site `this`, object-method traps, callback use, and interview caveats.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Write one createCounter() example and trace which outer variable binding is captured.',
                'Explain why the inner function keeps access after the outer function returns.',
                'Compare var and let loop behavior with one short callback example.',
                'Add one stale-closure note for async callbacks, timers, event handlers, debounce, throttle, or React hooks.',
                'Practice a two-minute answer that separates lexical scope, captured binding, practical use, and interview traps.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'Rewrite one unsafe SQL example using query builder or raw SQL bindings.',
                'Add allowlist validation for one dynamic sort, column, or table identifier.',
                'Add tests for OR 1=1, quoted comments, and unsafe sort inputs.',
                'Practice explaining why escaping alone is weaker than parameter binding.',
                'Practice a two-minute answer that names input boundary, fixed SQL structure, bindings, allowlists, and evidence.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'Rewrite one state-changing GET route into POST, PUT, PATCH, or DELETE.',
                'Add or verify `@csrf` for one Blade form or XSRF header handling for one stateful AJAX flow.',
                'Document SameSite cookie behavior separately from CSRF token validation.',
                'Add tests for missing token, stale token, and no state mutation on failure.',
                'Practice a two-minute answer that names browser cookies, request intent, token proof, SameSite, and evidence.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'Rewrite one unsafe Blade or JavaScript output path with context-aware escaping.',
                'Replace or justify one `{!! !!}` block with a sanitizer or trusted-content boundary.',
                'Add payload tests for reflected, stored, and DOM-style rendering paths.',
                'Practice explaining why validation, escaping, sanitization, and CSP solve different parts of XSS risk.',
                'Practice a two-minute answer that names output context, safe rendering, sanitizer boundary, payload tests, and evidence.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationPack($pack)) {
            return [
                'Draw the authentication lifecycle from login through session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                'Add or verify login throttling, session regeneration after login, reset-token expiry, logout invalidation, and token revocation behavior.',
                'Write failure-path tests for brute force, stale reset token, reused reset token, old session reuse, logged-out session reuse, and revoked token reuse.',
                'Add a logging note that captures suspicious-login context without passwords, reset tokens, session IDs, or bearer tokens.',
                'Practice a two-minute answer that separates authentication lifecycle risks from authorization permission checks.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'Create one production readiness checklist for APP_DEBUG, APP_ENV, config cache, exception output, and log redaction.',
                'Add checks for exposed .env files, leaked secrets, public storage, directory listing, and key rotation ownership.',
                'Review CORS allowlists, security headers, HTTPS enforcement, cookie flags, and trusted proxy boundaries.',
                'Add smoke checks that fail release when a production setting is missing, unsafe, or broader than documented.',
                'Practice a two-minute answer that names unsafe defaults, environment drift, deploy checks, owners, and rollback evidence.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'Inventory one route group that accepts object IDs and map owner, tenant, role, and allowed actions.',
                'Replace one direct model lookup with an owner-scoped or tenant-scoped query before returning the object.',
                'Add a policy, Gate, or service authorization check for the exact object and action.',
                'Add cross-user or cross-tenant tests for read, update, delete, download, export, and nested-resource access.',
                'Practice a two-minute answer that separates authentication from object-level authorization and names the Laravel files that enforce it.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'Classify the client as public or confidential before choosing Authorization Code with PKCE, Client Credentials, or another flow.',
                'Generate one high-entropy code_verifier and derive a S256 code_challenge without logging or rendering the verifier.',
                'Add callback checks for state, redirect URI, authorization code reuse, and token values appearing in the URL.',
                'Add failure tests for missing verifier, wrong verifier, expired verifier, reused code, and unsupported PKCE provider behavior.',
                'Practice a two-minute answer that compares Authorization Code with PKCE, Implicit Flow, and Client Credentials.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'Write one graph fixture and show the different visit order produced by BFS and DFS.',
                'Explain why BFS is the right fit for nearest result or shortest unweighted path, and why DFS is the right fit for branch exploration or subtree validation.',
                'Add a visited-set and cycle test so traversal cannot repeat work forever.',
                'Add max-depth, max-node, fan-out, batching, pagination, rate-limit, timeout, and memory notes for one API crawling case and one database hierarchy case.',
                'Practice a two-minute answer that separates traversal goal, queue versus stack, shortest-path behavior, production guardrails, and evidence.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesPack($pack, $filters)) {
            return [
                'Write a two-column comparison that separates Predictive AI output contracts from Generative AI output contracts.',
                'Add one Predictive AI example with input data, expected score or label, metric, and failure mode.',
                'Add one Generative AI example with prompt, context, expected output, quality check, and failure mode.',
                'Practice explaining why precision, recall, calibration, AUC, groundedness, citation quality, and human review are not interchangeable.',
                'Practice a two-minute answer that names product fit, evaluation evidence, and failure controls for both AI types.',
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                'Write a routing table that chooses RAG, Long Context, CAG, or hybrid routing from corpus freshness, size, stability, permissions, latency, and cost.',
                'Define the chatbot answer contract with selected_context_path, rag_pattern, token_budget, cache_version, source_version, citations, fallback_reason, and missing_evidence.',
                'Add one router example that selects RAG for changing corpora, Long Context for bounded document packs, CAG for stable cached knowledge, and hybrid routing for mixed support flows.',
                'Add tests for tenant-scoped retrieval, packed-context token limits, CAG cache invalidation, stale sources, missing citations, and low-confidence fallback.',
                'Practice a two-minute answer that separates RAG, Long Context, CAG, hybrid routing, answer contract, guardrails, and verification evidence.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesPack($pack, $filters)) {
            return [
                'Capture before and after EXPLAIN (ANALYZE, BUFFERS) output for the hot query and record Heap Fetches.',
                'Design one covering index with filter/order columns as keys and only projected hot-query fields in INCLUDE.',
                'Add a visibility-map note that names VACUUM or autovacuum expectations before expecting stable Index Only Scan behavior.',
                'Estimate index size, bloat risk, and write overhead before promoting the migration.',
                'Practice a two-minute answer that connects heap fetch cost, INCLUDE syntax, visibility map health, and rollback evidence.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($pack, $filters)) {
            return [
                'Write the protected invariant before code: no negative stock, no double spend, or no duplicate workflow transition.',
                'Place `lockForUpdate()` inside `DB::transaction()` before reading and mutating the protected value.',
                'Prove the lock query is selective and index-backed instead of locking broad table ranges.',
                'Add failure-path evidence for insufficient state, deadlock retry, lock timeout, and hot-row contention.',
                'Practice a two-minute answer that connects transaction boundary, row lock, deadlock, contention, and verification evidence.',
            ];
        }

        return [
            sprintf('Open the `%s` implementation lab and redo one source record without looking at the snippet first.', $technology),
            'Add one missing failure-path test to the generated example.',
            'Explain why each changed file belongs in its Laravel layer.',
            'Rerun verification commands and save the evidence in the portfolio artifact.',
            'Practice a two-minute spoken explanation using the interview pack.',
        ];
    }

    /**
     * Build a graph traversal rubric for BFS versus DFS decisions.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function graphTraversalRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Traversal goal',
                'points' => 25,
                'evidence' => 'Interview outline starts from nearest result, shortest unweighted path, branch exploration, dependency reasoning, or subtree validation.',
                'pass_signal' => 'Can choose BFS or DFS from the problem goal instead of saying one is always faster.',
            ],
            [
                'criterion' => 'State model',
                'points' => 20,
                'evidence' => 'Interview outline compares queue frontier with stack or recursion path.',
                'pass_signal' => 'Can explain visit order, queue versus stack, recursion depth, and memory cost.',
            ],
            [
                'criterion' => 'Cycle safety',
                'points' => 20,
                'evidence' => 'Practice script and quality plan require visited set, cycle checks, max depth, and max nodes.',
                'pass_signal' => 'Can prevent repeated work or infinite loops on cyclic graphs and shared dependencies.',
            ],
            [
                'criterion' => 'System fit',
                'points' => 20,
                'evidence' => 'Interview outline maps traversal choices to API crawling and database hierarchy examples.',
                'pass_signal' => 'Can name batching, pagination, rate limits, fan-out, timeouts, and memory guardrails for production use.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with goal, BFS/DFS choice, state model, guardrails, and evidence.',
            ],
        ];
    }

    /**
     * Build a React-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function reactRenderRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Profiler baseline',
                'points' => 25,
                'evidence' => 'Interview outline requires before/after React Profiler commit duration and rendered component count.',
                'pass_signal' => 'Can explain what was measured before choosing memoization.',
            ],
            [
                'criterion' => 'Memoization fit',
                'points' => 25,
                'evidence' => 'Interview outline separates React.memo, useMemo, and useCallback responsibilities.',
                'pass_signal' => 'Chooses the tool from prop stability, derived-value cost, or callback identity instead of using all three blindly.',
            ],
            [
                'criterion' => 'Structural render control',
                'points' => 20,
                'evidence' => 'Interview outline names state locality, context splitting, and virtualization as non-memo fixes.',
                'pass_signal' => 'Can identify when state placement or large-list rendering matters more than memo.',
            ],
            [
                'criterion' => 'Dependency safety',
                'points' => 15,
                'evidence' => 'Improvement tasks require dependency-array and stale-closure review.',
                'pass_signal' => 'Can explain how incorrect dependencies create stale UI.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with measurement, chosen tool, tradeoff, and verification evidence.',
            ],
        ];
    }

    /**
     * Build a JavaScript closure-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function javascriptClosureRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Lexical scope model',
                'points' => 25,
                'evidence' => 'Interview outline defines closure as function plus access to variables from lexical scope.',
                'pass_signal' => 'Can point to the outer scope, inner function, and captured variables in a code sample.',
            ],
            [
                'criterion' => 'Captured binding behavior',
                'points' => 25,
                'evidence' => 'Interview outline explains that the function keeps access to the variable binding, not a one-time copied value.',
                'pass_signal' => 'Can trace repeated createCounter() calls and explain why state persists.',
            ],
            [
                'criterion' => 'Practical usage',
                'points' => 20,
                'evidence' => 'Interview outline names private state, event handlers, callbacks, debounce, throttle, memoization, and hook closures.',
                'pass_signal' => 'Can name at least three real closure uses without treating closure as syntax trivia.',
            ],
            [
                'criterion' => 'Interview traps',
                'points' => 15,
                'evidence' => 'Improvement tasks require var versus let and stale-closure review.',
                'pass_signal' => 'Can explain var loop binding, let block binding, stale async callbacks, and hook dependency risk.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with lexical scope, captured binding, example output, practical use, and caveats.',
            ],
        ];
    }

    /**
     * Build an arrow-function `this` specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function javascriptArrowThisRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Lexical this model',
                'points' => 25,
                'evidence' => 'Interview outline states that arrow functions do not create their own `this`.',
                'pass_signal' => 'Can point to the surrounding scope where the arrow reads `this`.',
            ],
            [
                'criterion' => 'Call-site comparison',
                'points' => 25,
                'evidence' => 'Practice script contrasts normal function call-site `this` with arrow lexical `this`.',
                'pass_signal' => 'Can explain why `obj.normal()` and `obj.arrow()` do not bind `this` the same way.',
            ],
            [
                'criterion' => 'Binding traps',
                'points' => 20,
                'evidence' => 'Interview pack covers obj.arrow(), call, apply, and bind limitations.',
                'pass_signal' => 'Can explain why call/apply/bind cannot rebind arrow-function `this`.',
            ],
            [
                'criterion' => 'Practical callback use',
                'points' => 15,
                'evidence' => 'Improvement tasks require a timer, event handler, array callback, or class callback example.',
                'pass_signal' => 'Can name when an arrow callback helps and when a normal method is safer.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with lexical this, call-site this, trap, callback use, and caveat.',
            ],
        ];
    }

    /**
     * Build a PHP runtime-memory rubric for stack versus heap explanations.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function phpRuntimeMemoryRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Call-frame model',
                'points' => 25,
                'evidence' => 'Interview outline requires function calls, parameters, local variables, return values, and frame cleanup.',
                'pass_signal' => 'Can explain the stack as active call frames without overclaiming manual stack allocation in PHP.',
            ],
            [
                'criterion' => 'Heap-backed data',
                'points' => 25,
                'evidence' => 'Interview outline requires arrays, objects, strings, references, and object graphs.',
                'pass_signal' => 'Can explain why large arrays or objects create memory pressure beyond one call frame.',
            ],
            [
                'criterion' => 'Cleanup and references',
                'points' => 20,
                'evidence' => 'Improvement tasks require unset(), scope exit, memory_get_usage(), and PHP caveats.',
                'pass_signal' => 'Can connect scope exit, references, reference counting, and GC without describing raw pointer management.',
            ],
            [
                'criterion' => 'Failure modes',
                'points' => 15,
                'evidence' => 'Interview outline names deep recursion, large arrays, retained references, circular references, or stale worker state.',
                'pass_signal' => 'Can name at least one practical PHP memory risk and how to spot it.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer that separates stack frames, heap data, references, cleanup, and PHP runtime caveats.',
            ],
        ];
    }

    /**
     * Detect arrow-function `this` content inside the broader JavaScript closure lane.
     */
    private function isArrowThisPack(array $pack): bool
    {
        $haystack = strtolower((string) json_encode($pack, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect broken-authentication work inside the broader auth-security lane.
     */
    private function isBrokenAuthenticationPack(array $pack): bool
    {
        $haystack = strtolower((string) json_encode($pack, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }

    /**
     * Build a SQL Injection-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function sqlInjectionRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Injection mechanism',
                'points' => 20,
                'evidence' => 'Interview outline explains user input becoming SQL logic.',
                'pass_signal' => 'Can explain SQL Injection without only naming a payload.',
            ],
            [
                'criterion' => 'Parameterized values',
                'points' => 25,
                'evidence' => 'Interview outline requires query builder, Eloquent, or raw SQL bindings.',
                'pass_signal' => 'Can show how values are bound separately from SQL structure.',
            ],
            [
                'criterion' => 'Identifier allowlists',
                'points' => 20,
                'evidence' => 'Interview outline distinguishes values from dynamic table, column, and sort identifiers.',
                'pass_signal' => 'Can explain why identifiers need allowlists instead of value bindings.',
            ],
            [
                'criterion' => 'Attack-payload tests',
                'points' => 20,
                'evidence' => 'Improvement tasks require malicious payload and unsafe sort tests.',
                'pass_signal' => 'Can test OR 1=1, comment probes, and invalid identifier inputs.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise interview answer with mechanism, prevention, review, and verification evidence.',
            ],
        ];
    }

    /**
     * Build an IDOR-specific 100-point rubric for object-level authorization.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function idorAccessControlRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Object surface inventory',
                'points' => 20,
                'evidence' => 'Interview outline maps object routes, identifiers, owner or tenant boundary, roles, and allowed actions.',
                'pass_signal' => 'Can identify every URL, API, download, export, and nested resource that accepts a user-controlled object identifier.',
            ],
            [
                'criterion' => 'Scoped lookup',
                'points' => 25,
                'evidence' => 'Workbench output requires replacing direct findOrFail() with owner-scoped, tenant-scoped, or relationship-scoped lookup.',
                'pass_signal' => 'Can explain why authentication alone does not make Product::find($id) or Order::findOrFail($id) safe.',
            ],
            [
                'criterion' => 'Object authorization',
                'points' => 25,
                'evidence' => 'Quality plan requires policy, Gate, or service authorization on the exact object and action.',
                'pass_signal' => 'Can show where Laravel checks view, update, delete, download, export, and nested-resource permissions.',
            ],
            [
                'criterion' => 'Cross-user denial tests',
                'points' => 15,
                'evidence' => 'Improvement tasks require attacker ID-swap tests across user, tenant, and role boundaries.',
                'pass_signal' => 'Can prove user A cannot access user B data even when the identifier is guessed or copied from another response.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with IDOR mechanism, scoped lookup, object policy, denial tests, 403 or 404 decision, and monitoring evidence.',
            ],
        ];
    }

    /**
     * Build a CSRF-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function csrfProtectionRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Attack mechanism',
                'points' => 20,
                'evidence' => 'Interview outline explains a logged-in browser sending an unwanted state-changing request.',
                'pass_signal' => 'Can explain CSRF without confusing it with stealing cookies or XSS.',
            ],
            [
                'criterion' => 'Token proof',
                'points' => 25,
                'evidence' => 'Interview outline requires CSRF token proof tied to the intended application flow.',
                'pass_signal' => 'Can show where the token is generated, sent, and verified.',
            ],
            [
                'criterion' => 'Cookie boundary',
                'points' => 20,
                'evidence' => 'Interview outline separates SameSite cookie behavior from token validation.',
                'pass_signal' => 'Can explain why SameSite reduces exposure but does not replace token validation.',
            ],
            [
                'criterion' => 'Failure tests',
                'points' => 20,
                'evidence' => 'Improvement tasks require missing-token, stale-token, and unsafe method tests.',
                'pass_signal' => 'Can test 419 or equivalent failure without mutating server state.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with browser intent, token proof, SameSite tradeoff, and verification evidence.',
            ],
        ];
    }

    /**
     * Build an XSS-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function xssDefenseRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Attack variants',
                'points' => 20,
                'evidence' => 'Interview outline explains reflected, stored, and DOM-based XSS paths.',
                'pass_signal' => 'Can explain XSS as untrusted data reaching an executable browser context.',
            ],
            [
                'criterion' => 'Context-aware escaping',
                'points' => 25,
                'evidence' => 'Interview outline requires escaped Blade output and safe JavaScript serialization.',
                'pass_signal' => 'Can choose the correct defense for HTML text, attributes, JavaScript data, URLs, and rich text.',
            ],
            [
                'criterion' => 'Raw HTML trust boundary',
                'points' => 20,
                'evidence' => 'Interview outline distinguishes escaped text from sanitized or trusted HTML.',
                'pass_signal' => 'Can explain why `{!! !!}` is dangerous unless content is sanitized or fully trusted.',
            ],
            [
                'criterion' => 'Payload rendering tests',
                'points' => 20,
                'evidence' => 'Improvement tasks require reflected, stored, and DOM-style payload tests.',
                'pass_signal' => 'Can prove script payloads render as harmless text or are removed by the sanitizer.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with XSS variant, output context, safe rendering, tests, and CSP limits.',
            ],
        ];
    }

    /**
     * Build a Broken Authentication-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function brokenAuthenticationRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Lifecycle model',
                'points' => 25,
                'evidence' => 'Interview outline covers login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                'pass_signal' => 'Can explain authentication as a lifecycle instead of only a successful login form.',
            ],
            [
                'criterion' => 'Session and reset controls',
                'points' => 25,
                'evidence' => 'Implementation evidence requires throttling, session regeneration, reset-token expiry, logout invalidation, and token revocation.',
                'pass_signal' => 'Can point to Laravel controls that reduce session fixation, brute force, stale reset token, and token reuse risk.',
            ],
            [
                'criterion' => 'Failure-path tests',
                'points' => 20,
                'evidence' => 'Improvement tasks require tests for brute force, stale reset token, reused reset token, old session reuse, logged-out session reuse, and revoked token reuse.',
                'pass_signal' => 'Can prove unsafe authentication states fail closed instead of only testing happy login.',
            ],
            [
                'criterion' => 'Sensitive auth logging',
                'points' => 15,
                'evidence' => 'Quality and portfolio evidence require suspicious-login logging without passwords, reset tokens, session IDs, or bearer tokens.',
                'pass_signal' => 'Can name useful auth log fields without leaking credentials or reusable identity secrets.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer that separates authentication identity proof from authorization permission checks.',
            ],
        ];
    }

    /**
     * Build a Security Misconfiguration-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function securityMisconfigurationRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Unsafe default detection',
                'points' => 20,
                'evidence' => 'Interview outline requires debug mode, verbose errors, exposed secrets, default credentials, and public storage review.',
                'pass_signal' => 'Can identify which local-safe settings become dangerous in production.',
            ],
            [
                'criterion' => 'Environment drift control',
                'points' => 20,
                'evidence' => 'Interview outline separates local, staging, CI, and production expectations.',
                'pass_signal' => 'Can document expected values and owners for each environment-specific setting.',
            ],
            [
                'criterion' => 'Boundary hardening',
                'points' => 25,
                'evidence' => 'Improvement tasks require CORS, security headers, HTTPS, cookie flags, trusted proxies, and storage visibility checks.',
                'pass_signal' => 'Can explain why each boundary setting reduces a concrete exposure path.',
            ],
            [
                'criterion' => 'Deployment smoke checks',
                'points' => 20,
                'evidence' => 'Quality plan requires fail-closed smoke checks for unsafe production settings.',
                'pass_signal' => 'Can prove release blocks when debug mode, exposed .env, broad CORS, missing headers, or proxy drift is detected.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with misconfiguration mechanism, Laravel audit areas, readiness checks, owners, and rollback evidence.',
            ],
        ];
    }

    /**
     * Build an OAuth-specific 100-point rubric.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function oauthFlowRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Flow fit',
                'points' => 20,
                'evidence' => 'Interview outline distinguishes public clients, confidential clients, user delegation, and machine-to-machine access.',
                'pass_signal' => 'Can choose Authorization Code with PKCE, Client Credentials, or another flow from client type and trust boundary.',
            ],
            [
                'criterion' => 'PKCE proof',
                'points' => 25,
                'evidence' => 'Workbench output includes code_verifier generation, S256 code_challenge, and verifier privacy rules.',
                'pass_signal' => 'Can explain why the code_challenge is sent in the authorize request while code_verifier stays private until token exchange.',
            ],
            [
                'criterion' => 'Callback validation',
                'points' => 20,
                'evidence' => 'Quality plan requires state, redirect URI, code reuse, and callback token-leakage validation.',
                'pass_signal' => 'Can reject state mismatch, unexpected redirect URI, reused code, and access_token or id_token values in browser callback input.',
            ],
            [
                'criterion' => 'Token boundary',
                'points' => 20,
                'evidence' => 'Improvement tasks require audience, scope, token lifetime, refresh rotation, and storage-boundary review.',
                'pass_signal' => 'Can state where tokens are exchanged, stored, refreshed, logged, and never exposed to the browser URL.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with client type, chosen flow, PKCE proof, callback checks, and token boundary evidence.',
            ],
        ];
    }

    /**
     * Build an AI type comparison rubric for Predictive AI versus Generative AI.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function aiTypeComparisonRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Output contract',
                'points' => 25,
                'evidence' => 'Interview outline separates scores, labels, forecasts, and rankings from generated text, code, images, summaries, and answers.',
                'pass_signal' => 'Can classify a product use case as prediction, generation, or a pipeline containing both.',
            ],
            [
                'criterion' => 'Input evidence',
                'points' => 20,
                'evidence' => 'Interview outline separates historical data and labels from prompt, context, retrieved evidence, and constraints.',
                'pass_signal' => 'Can explain why training data, live features, prompt context, and retrieval evidence play different roles.',
            ],
            [
                'criterion' => 'Evaluation fit',
                'points' => 25,
                'evidence' => 'Interview outline requires predictive metrics and generative quality checks as separate evidence.',
                'pass_signal' => 'Can choose precision, recall, calibration, AUC, error rate, business lift, groundedness, safety, citations, tests, or human review based on output type.',
            ],
            [
                'criterion' => 'Failure modes',
                'points' => 15,
                'evidence' => 'Interview outline names drift, overfitting, biased labels, hallucination, fabricated citations, prompt injection, and unsafe code.',
                'pass_signal' => 'Can name at least one failure mode and control for each AI type.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer that separates product fit, output contract, metrics, and risk controls.',
            ],
        ];
    }

    /**
     * Build a RAG context-strategy rubric for chatbot architecture decisions.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function ragContextStrategyRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Context strategy fit',
                'points' => 25,
                'evidence' => 'Interview outline compares RAG, Long Context, CAG, and hybrid routing before selecting an implementation path.',
                'pass_signal' => 'Can choose the context strategy from corpus freshness, size, stability, permissions, latency, and cost.',
            ],
            [
                'criterion' => 'Answer contract',
                'points' => 20,
                'evidence' => 'Interview outline names selected_context_path, rag_pattern, token_budget, cache_version, source_version, fallback_reason, and missing_evidence.',
                'pass_signal' => 'Can describe the response fields that make a chatbot answer auditable after generation.',
            ],
            [
                'criterion' => 'Router guardrails',
                'points' => 20,
                'evidence' => 'Practice script requires context routing, citations, permissions, token budget, cache version, and source freshness.',
                'pass_signal' => 'Can explain how retrieval filters, cache keys, packed documents, and citations stay scoped by user, tenant, permission, locale, and content version.',
            ],
            [
                'criterion' => 'Failure and evaluation tests',
                'points' => 20,
                'evidence' => 'Interview outline requires tests for retrieval permission filters, token budgets, CAG invalidation, stale cache fallback, and source markers.',
                'pass_signal' => 'Can prove stale, unauthorized, missing, oversized, or low-confidence context fails closed or triggers fallback.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer that separates RAG, Long Context, CAG, hybrid routing, answer contract, and evidence.',
            ],
        ];
    }

    /**
     * Build a covering-index rubric for PostgreSQL heap-fetch optimization.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function coveringIndexRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Plan baseline',
                'points' => 25,
                'evidence' => 'Interview outline requires EXPLAIN (ANALYZE, BUFFERS), Heap Fetches, and buffer-read evidence.',
                'pass_signal' => 'Can prove whether the current query uses Index Scan, Index Only Scan, or heap fetches before changing indexes.',
            ],
            [
                'criterion' => 'INCLUDE design',
                'points' => 25,
                'evidence' => 'Interview outline separates key columns for filtering and ordering from INCLUDE columns for projected values.',
                'pass_signal' => 'Can choose key columns and included columns from one hot query without turning the index into a copy of the table.',
            ],
            [
                'criterion' => 'Visibility health',
                'points' => 20,
                'evidence' => 'Practice script names visibility map, VACUUM, and autovacuum as part of Index Only Scan behavior.',
                'pass_signal' => 'Can explain why Index Only Scan may still touch the heap when visibility information is not current.',
            ],
            [
                'criterion' => 'Cost guardrails',
                'points' => 15,
                'evidence' => 'Interview outline requires index size, bloat risk, write overhead, and rollback evidence.',
                'pass_signal' => 'Can state the operational cost of the index and when not to use INCLUDE.',
            ],
            [
                'criterion' => 'Communication',
                'points' => 15,
                'evidence' => implode(' ', $pack['practice_script']),
                'pass_signal' => 'Gives a concise answer with heap-fetch mechanism, covering-index design, visibility-map caveat, and verification evidence.',
            ],
        ];
    }

    /**
     * Build a database-locking rubric for transaction-safe concurrency control.
     *
     * @return array<int, array{criterion: string, points: int, evidence: string, pass_signal: string}>
     */
    private function databaseLockingRubric(array $pack): array
    {
        return [
            [
                'criterion' => 'Protected invariant',
                'points' => 25,
                'evidence' => 'Interview outline names the race condition and invariant before choosing a lock.',
                'pass_signal' => 'Can explain what bad concurrent state is being prevented, such as oversold inventory or double-spent balance.',
            ],
            [
                'criterion' => 'Transaction boundary',
                'points' => 25,
                'evidence' => 'Interview outline requires `DB::transaction()` and `lockForUpdate()` before checking or mutating protected state.',
                'pass_signal' => 'Can place the row lock inside the same transaction as read, validation, and write.',
            ],
            [
                'criterion' => 'Lock scope and ordering',
                'points' => 20,
                'evidence' => 'Interview outline requires selective indexed lookup, short critical sections, and consistent lock ordering.',
                'pass_signal' => 'Can avoid broad table locks, long waits, external calls inside transactions, and avoidable deadlocks.',
            ],
            [
                'criterion' => 'Failure and operations evidence',
                'points' => 20,
                'evidence' => 'Improvement tasks require insufficient-state, deadlock retry, timeout, hot-row contention, and lock-wait monitoring evidence.',
                'pass_signal' => 'Can prove concurrent requests fail closed and knows what operational signal to watch after release.',
            ],
            [
                'criterion' => 'Interview clarity',
                'points' => 10,
                'evidence' => implode(' ', $pack['practice_script'] ?? []),
                'pass_signal' => 'Can answer in two minutes without saying locks magically make every query safe or fast.',
            ],
        ];
    }
}
