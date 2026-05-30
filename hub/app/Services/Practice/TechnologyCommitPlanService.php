<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyCommitPlanService
{
    /**
     * Create commit-ready artifacts from a technology implementation lab.
     */
    public function __construct(
        private readonly TechnologyImplementationLabService $labs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a branch, commit, evidence, and review plan for one technology lab.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $lab = $this->labs->build($technology, $filters);
        $slug = Str::of($technology)->slug()->toString();
        $changedFiles = $this->changedFiles($lab);

        return [
            'title' => sprintf('Technology Commit Plan: %s', $technology),
            'technology' => $technology,
            'practice' => $lab['practice'],
            'related_workbench' => $lab['related_workbench'],
            'branch' => sprintf('practice/%s/content-backed-implementation', $slug),
            'commit_message' => sprintf('practice: implement %s content-backed lab', $slug),
            'lab' => $lab,
            'changed_files' => $changedFiles,
            'verification' => $lab['commands'],
            'evidence_checklist' => $this->evidenceChecklist($lab, $changedFiles),
            'review_checklist' => $this->reviewChecklist($technology, $lab),
            'next_actions' => $this->nextActionsFor($technology, $lab['meta']['filters']),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Create branch',
                'Implement files from source examples',
                'Run verification commands',
                'Capture evidence checklist',
                'Write commit message',
                'Review changed files',
            ]),
        ];
    }

    /**
     * Extract unique changed files from all source-example snippets.
     *
     * @return array<int, string>
     */
    private function changedFiles(array $lab): array
    {
        return collect($lab['source_examples']['items'])
            ->flatMap(fn (array $item): array => collect($item['snippets'])
                ->pluck('file')
                ->all())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Build evidence items that prove the technology implementation is complete.
     *
     * @param  array<int, string>  $changedFiles
     * @return array<int, string>
     */
    private function evidenceChecklist(array $lab, array $changedFiles): array
    {
        if (($lab['technology'] ?? null) === 'react-render-performance') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'React Profiler baseline and after-state evidence are captured before claiming improvement.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'The recommendation names why React.memo, useMemo, useCallback, state locality, or virtualization was chosen.',
            ];
        }

        if (($lab['technology'] ?? null) === 'javascript-closures' && $this->isArrowThisLab($lab)) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Arrow-function examples prove lexical this, normal function dynamic this, object-method traps, and callback use before claiming interview readiness.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Interview evidence covers obj.arrow(), call/apply/bind limits, timer callbacks, class callbacks, and when not to use arrow methods.',
            ];
        }

        if (($lab['technology'] ?? null) === 'javascript-closures') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Closure examples trace lexical scope, captured bindings, and repeated-call state before claiming interview readiness.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Interview evidence covers createCounter(), private state, var versus let, stale closures, and real callback usage.',
            ];
        }

        if (($lab['technology'] ?? null) === 'sql-injection-defense') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Unsafe SQL examples are replaced with parameterized values or query builder bindings.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Identifier allowlists and malicious payload tests prove user input stays data.',
            ];
        }

        if (($lab['technology'] ?? null) === 'csrf-protection') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'State-changing browser flows include CSRF token proof or an explicit non-cookie auth reason.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'SameSite review and missing-token tests prove browser cookies alone cannot mutate state.',
            ];
        }

        if (($lab['technology'] ?? null) === 'xss-defense') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Untrusted browser output is rendered with escaped Blade output, safe JSON handoff, or sanitized rich text.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Payload tests prove reflected, stored, and DOM-style input cannot execute script.',
            ];
        }

        if (($lab['technology'] ?? null) === 'auth-security' && $this->isBrokenAuthenticationLab($lab)) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Authentication lifecycle evidence covers login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Failure-path evidence proves throttling, session regeneration, reset-token expiry, logout invalidation, token revocation, and sensitive-log redaction.',
            ];
        }

        if (($lab['technology'] ?? null) === 'security-misconfiguration') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Production configuration readiness checks cover debug mode, environment drift, exposed secrets, storage visibility, and verbose errors.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'CORS, security headers, cookie flags, trusted proxies, HTTPS enforcement, owners, and rollback notes prove the release fails closed.',
            ];
        }

        if (($lab['technology'] ?? null) === 'idor-access-control') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Object-level authorization evidence covers route inventory, scoped lookup, policy checks, and attacker ID-swap denial tests.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                '403 versus 404 rationale, nested-resource ownership, downloads, exports, logs, and monitoring proof are captured.',
            ];
        }

        if (($lab['technology'] ?? null) === 'oauth-flow') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Authorization Code with PKCE is chosen from client type, secret-storage ability, and user delegation needs.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Verifier failure and callback token-leakage tests prove the OAuth flow fails closed.',
            ];
        }

        if (($lab['technology'] ?? null) === 'graph-traversal') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'BFS or DFS is chosen from traversal goal instead of a universal speed claim.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Traversal-order, visited-set, cycle, depth, fan-out, API crawling, database hierarchy, and memory guardrails are captured.',
            ];
        }

        if (($lab['technology'] ?? null) === 'php' && PhpRuntimeMemoryTopicService::matchesLab($lab)) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'The example separates stack call frames from heap-backed arrays, objects, strings, and references.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Cleanup notes cover scope exit, unset(), reference counting, GC, and one PHP memory failure mode.',
            ];
        }

        if (($lab['technology'] ?? null) === 'llm-foundations' && $this->isAgentMemoryLab($lab)) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'AI agent memory is separated into working, episodic, semantic, and procedural contracts before storage is discussed.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Governance evidence covers source, freshness, confidence, permission, retention, correction path, stale-memory fallback, and private-memory blocking.',
            ];
        }

        if (($lab['technology'] ?? null) === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($this->phaseHaystack($lab))) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Predictive AI and Generative AI outputs are separated by output contract before model capability is discussed.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Quality evidence includes predictive metrics, generative review checks, and failure modes for both sides.',
            ];
        }

        if (($lab['technology'] ?? null) === 'rag-systems') {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Chatbot context strategy evidence chooses RAG, Long Context, CAG, or hybrid routing before selecting the retrieval pattern.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Evidence covers context contract fields, source freshness, tenant permissions, token budget, cache version, citations, fallback behavior, and missing-evidence handling.',
            ];
        }

        if (($lab['technology'] ?? null) === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($lab)) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Concurrency evidence proves the protected invariant, transaction boundary, row lock, and failure path before claiming safety.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'Deadlock retry, lock timeout, hot-row contention, indexed lookup, and short critical-section notes are captured.',
            ];
        }

        if (($lab['technology'] ?? null) === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact($lab)) {
            return [
                sprintf('Source records covered: %d.', $lab['meta']['task_count']),
                'Before and after EXPLAIN (ANALYZE, BUFFERS) output proves whether Heap Fetches dropped.',
                sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
                sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
                'INCLUDE column rationale, visibility-map health, VACUUM expectations, bloat risk, write overhead, and rollback notes are captured.',
            ];
        }

        return [
            sprintf('Source records covered: %d.', $lab['meta']['task_count']),
            sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
            sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
            'Workspace links were opened for each selected source record.',
            'No JSON content file was changed to fake implementation progress.',
        ];
    }

    /**
     * Build review checks specific to content-backed technology work.
     *
     * @return array<int, string>
     */
    private function reviewChecklist(string $technology, array $lab = []): array
    {
        if ($technology === 'react-render-performance') {
            return [
                'Profiler evidence exists before and after the optimization.',
                'React.memo is used only for expensive children with stable props.',
                'useMemo and useCallback dependency arrays are complete and do not create stale UI.',
                'State locality, context splitting, pagination, or virtualization were considered before blanket memoization.',
                'The code remains readable and does not memoize cheap components just to silence render counts.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisLab($lab)) {
            return [
                'The answer states that arrow functions do not create their own `this`.',
                'The example contrasts lexical `this` with normal function call-site `this`.',
                '`obj.arrow()` is documented as a trap because the object call does not rebind arrow `this`.',
                '`call`, `apply`, and `bind` limitations are covered explicitly for arrow functions.',
                'Practical guidance names where arrow callbacks help and where object methods or constructors should stay normal functions.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'The closure definition names lexical scope and captured variable bindings.',
                'The example traces createCounter() or another function factory across repeated calls.',
                'The answer distinguishes captured bindings from one-time copied values.',
                'Interview traps include var versus let loop behavior and stale closures in async callbacks, timers, or hooks.',
                'Practical uses include private state, event handlers, debounce, throttle, memoization, or callbacks.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'No request input is concatenated into raw SQL strings.',
                'All user-controlled values use Eloquent, query builder parameters, or raw SQL bindings.',
                'Dynamic identifiers such as sort columns, table names, and directions use allowlists.',
                'Tests cover OR 1=1, quoted comments, UNION-like probes, and invalid sort inputs.',
                'The interview answer explains why escaping alone is not the primary defense.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'State-changing routes do not use GET.',
                'Blade forms include `@csrf` or stateful JavaScript requests send the expected XSRF token/header.',
                'SameSite cookie settings are deliberate and do not replace token verification.',
                'Tests cover missing token, stale token, and no mutation on failed CSRF checks.',
                'The interview answer explains why browser cookies create CSRF risk.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'Untrusted values use escaped Blade output or another context-specific encoder.',
                '`{!! !!}` appears only behind a sanitizer, trusted-content boundary, or explicit removal decision.',
                'Server data passed to JavaScript uses safe JSON serialization instead of string concatenation.',
                'Tests cover reflected, stored, and DOM-style script payloads as harmless rendered data.',
                'The interview answer explains why CSP is defense-in-depth, not the primary XSS fix.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationLab($lab)) {
            return [
                'The review treats authentication as a lifecycle, not only a successful login form.',
                'Login throttling and suspicious-login logging are covered without storing passwords, reset tokens, session IDs, or bearer tokens.',
                'Session regeneration, logout invalidation, and old-session rejection are verified against session fixation and reuse.',
                'Password reset tests reject stale, reused, or mismatched reset tokens.',
                'Token refresh, token revocation, remember-me, and MFA recovery paths fail closed and stay separate from authorization checks.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'APP_DEBUG, APP_ENV, exception output, config cache, and log redaction are checked for production readiness.',
                '.env exposure, leaked secrets, public storage, directory listing, and default credentials have explicit release blockers.',
                'CORS origins, security headers, HTTPS enforcement, session cookie flags, and trusted proxies are reviewed as exposure boundaries.',
                'Smoke checks fail the release when production configuration is missing, unsafe, or broader than documented.',
                'Every environment-specific setting has an owner, expected value, rollback note, and verification evidence.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'Every route that accepts an object identifier has an owner, tenant, role, and allowed-action review.',
                'Controllers do not return models from direct findOrFail() before scoped lookup or policy authorization.',
                'Policies, Gates, or service checks authorize the exact object, not just the authenticated user.',
                'Tests prove user A cannot read, update, delete, download, export, or access nested resources owned by user B.',
                'The interview answer explains why IDOR is broken object-level authorization and how Laravel layers prevent it.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'Public clients use Authorization Code with PKCE instead of Implicit Flow.',
                'The authorize URL sends code_challenge and code_challenge_method=S256, never code_verifier.',
                'Callback handling validates state, redirect URI, authorization code reuse, and token-field leakage.',
                'Tests cover missing verifier, wrong verifier, expired verifier, reused code, and unsupported PKCE providers.',
                'Token audience, scope, lifetime, refresh rotation, storage boundary, and log redaction are documented.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'The answer chooses BFS or DFS from nearest result, shortest path, branch exploration, dependency reasoning, or subtree validation.',
                'BFS examples use a queue frontier and DFS examples use stack or recursion path evidence.',
                'Visited-set behavior is tested with a cyclic graph or shared dependency fixture.',
                'API crawling notes include hop limits, pagination, batching, rate limits, timeouts, and stop conditions.',
                'Database hierarchy notes include depth limits, fan-out limits, memory pressure, and subtree validation tradeoffs.',
            ];
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesLab($lab)) {
            return [
                'The answer treats stack and heap as a PHP runtime mental model, not manual allocation controls.',
                'Function examples identify parameters, local variables, return values, and frame cleanup.',
                'Array, object, string, or object-graph examples identify heap-backed memory pressure and references.',
                'Cleanup notes distinguish scope exit, unset(), reference counting, and garbage collection.',
                'Production risk covers deep recursion, large arrays, retained references, circular references, or long-running worker state.',
            ];
        }

        if ($technology === 'llm-foundations' && $this->isAgentMemoryLab($lab)) {
            return [
                'The plan does not treat memory as one prompt-history bucket.',
                'Working memory is bounded to the current task and is not promoted into durable storage without review.',
                'Episodic memory cannot cross user, repo, project, or permission boundaries without explicit authorization.',
                'Semantic memory stores source, freshness, confidence, retention, and correction metadata for durable facts.',
                'Procedural memory points to reviewed playbooks, tests, commands, rollback steps, and mismatch checks.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($this->phaseHaystack($lab))) {
            return [
                'The explanation does not treat Predictive AI and Generative AI as interchangeable model labels.',
                'Predictive examples include scores, labels, forecasts, rankings, or risk decisions with appropriate metrics.',
                'Generative examples include prompt/context, generated output, groundedness, safety, citation, test, or human-review checks.',
                'Failure-mode review covers drift, overfitting, biased labels, hallucination, fabricated citations, prompt injection, and unsafe code.',
                'The interview answer can explain when a product needs prediction, generation, or both in one pipeline.',
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                'The plan chooses RAG, Long Context, CAG, or hybrid routing from corpus freshness, size, stability, permissions, latency, and cost.',
                'The context contract names selected_context_path, rag_pattern, token_budget, cache_version, source_version, citations, fallback_reason, and missing_evidence.',
                'Retrieval filters, cache keys, packed documents, and citations are scoped by user, tenant, permission, locale, and content version.',
                'Tests cover CAG cache invalidation, Long Context token limits, unauthorized retrieval chunks, stale sources, missing citations, and hybrid route selection.',
                'The interview answer explains why RAG, Long Context, and CAG are complementary context strategies instead of interchangeable buzzwords.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($lab)) {
            return [
                'The protected invariant is written before code review, such as no negative stock, no double spend, or one terminal state transition.',
                '`lockForUpdate()` is called inside `DB::transaction()` and before the protected row value is validated or mutated.',
                'The lock query is selective and index-backed so the fix does not accidentally lock too much data.',
                'External network calls, emails, queues, sleeps, and long computations are outside the locked transaction.',
                'Tests or review notes cover insufficient state, concurrent request replay, deadlock retry, lock timeout, and lock-wait monitoring.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact($lab)) {
            return [
                'The query-plan baseline captures selected columns, filters, ordering, row count, Index Scan or Index Only Scan, Heap Fetches, and buffer reads.',
                'Key columns support the hot query filter and order path before payload columns are considered.',
                'INCLUDE columns are limited to projected fields needed by the hot query and do not duplicate cold table data.',
                'Visibility map, VACUUM, or autovacuum health is documented before claiming stable Index Only Scan behavior.',
                'Index size, bloat risk, write overhead, rollback command, and rollout timing are reviewed before merge.',
            ];
        }

        return [
            sprintf('The implementation matches the inferred `%s` technology.', $technology),
            'Routes only map URLs and stay commented for learners.',
            'Controllers stay thin and delegate behavior to services or requests.',
            'Validation, authorization, storage, queue, or cache responsibilities live in the correct Laravel layer.',
            'Tests prove behavior from the selected JSON records instead of only checking implementation details.',
        ];
    }

    /**
     * Return route handoffs that naturally follow commit preparation.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, array{label: string, purpose: string, path: string, api_path: string|null}>
     */
    private function nextActionsFor(string $technology, array $filters): array
    {
        $query = http_build_query($this->queryFilters($filters));

        return [
            [
                'label' => 'Reopen implementation lab',
                'purpose' => 'Return to the ordered implementation phases if the commit evidence exposes a gap.',
                'path' => $this->path("/practice/technology-implementation-lab/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-implementation-lab/{$technology}", $query),
            ],
            [
                'label' => 'Create portfolio artifact',
                'purpose' => 'Convert the completed implementation and evidence into a reusable README-style artifact.',
                'path' => $this->path("/practice/technology-portfolio-artifact/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-portfolio-artifact/{$technology}", $query),
            ],
            [
                'label' => 'Practice interview defense',
                'purpose' => 'Prepare concise explanations for choices, tradeoffs, tests, and production risks.',
                'path' => $this->path("/practice/technology-interview-pack/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-interview-pack/{$technology}", $query),
            ],
            [
                'label' => 'Run skill assessment',
                'purpose' => 'Score readiness after the implementation evidence is committed.',
                'path' => $this->path("/practice/technology-skill-assessment/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-skill-assessment/{$technology}", $query),
            ],
            [
                'label' => 'Check mastery decision',
                'purpose' => 'Decide whether to promote the topic or repeat with remediation work.',
                'path' => $this->path("/practice/technology-mastery-checkpoint/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-mastery-checkpoint/{$technology}", $query),
            ],
        ];
    }

    /**
     * Detect agent-memory labs inside the broader LLM foundations lane.
     *
     * @param  array<string, mixed>  $lab
     */
    private function isAgentMemoryLab(array $lab): bool
    {
        return AiAgentMemoryTopicService::matchesText($this->phaseHaystack($lab))
            || (($lab['related_workbench']['path'] ?? null) === '/workbench/ai-agent-memory-plan');
    }

    /**
     * Build searchable text from implementation lab phases.
     *
     * @param  array<string, mixed>  $lab
     */
    private function phaseHaystack(array $lab): string
    {
        return implode(' ', collect($lab['phases'] ?? [])
            ->flatMap(fn (array $phase): array => [
                $phase['label'] ?? '',
                $phase['goal'] ?? '',
                ...($phase['tasks'] ?? []),
            ])
            ->all());
    }

    /**
     * Keep only filled filters for generated route handoffs.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, int|string>
     */
    private function queryFilters(array $filters): array
    {
        return collect($filters)
            ->reject(fn (mixed $value, string $key): bool => $key === 'technology')
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->map(fn (mixed $value): int|string => is_int($value) ? $value : (string) $value)
            ->all();
    }

    /**
     * Append a query string when filters are present.
     */
    private function path(string $path, string $query): string
    {
        return $query === '' ? $path : sprintf('%s?%s', $path, $query);
    }

    /**
     * Detect arrow-function `this` work inside the broader JavaScript closure lane.
     */
    private function isArrowThisLab(array $lab): bool
    {
        if (($lab['technology'] ?? null) !== 'javascript-closures') {
            return false;
        }

        $sourceText = collect($lab['source_examples']['items'] ?? [])
            ->flatMap(fn (array $item): array => [
                $item['content']['title'] ?? '',
                $item['content']['body'] ?? '',
                $item['task'] ?? '',
            ])
            ->implode(' ');

        $phaseText = collect($lab['phases'] ?? [])
            ->flatMap(fn (array $phase): array => [
                $phase['label'] ?? '',
                $phase['goal'] ?? '',
                ...($phase['tasks'] ?? []),
            ])
            ->implode(' ');

        $haystack = Str::lower($sourceText.' '.$phaseText);

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'lexical this') || str_contains($haystack, 'obj.arrow') || str_contains($haystack, 'call/apply/bind'));
    }

    /**
     * Detect broken-authentication work inside the broader auth-security lane.
     */
    private function isBrokenAuthenticationLab(array $lab): bool
    {
        if (($lab['technology'] ?? null) !== 'auth-security') {
            return false;
        }

        $sourceText = collect($lab['source_examples']['items'] ?? [])
            ->flatMap(fn (array $item): array => [
                $item['content']['title'] ?? '',
                $item['content']['body'] ?? '',
                $item['task'] ?? '',
            ])
            ->implode(' ');

        $phaseText = collect($lab['phases'] ?? [])
            ->flatMap(fn (array $phase): array => [
                $phase['label'] ?? '',
                $phase['goal'] ?? '',
                ...($phase['tasks'] ?? []),
            ])
            ->implode(' ');

        $haystack = Str::lower($sourceText.' '.$phaseText);

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }
}
