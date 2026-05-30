<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyEvidenceArchiveService
{
    /**
     * Create reusable archive entries from spaced reviews.
     */
    public function __construct(
        private readonly TechnologySpacedReviewService $reviews,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build an evidence archive entry for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $review = $this->reviews->build($technology, $filters);
        $archiveId = sprintf('tech-%s-%s', Str::slug($technology), now()->format('Ymd'));

        return [
            'title' => sprintf('Technology Evidence Archive: %s', $technology),
            'technology' => $technology,
            'archive_id' => $archiveId,
            'review' => $review,
            'retrieval_keys' => $this->retrievalKeys($technology, $review),
            'proof_bundle' => $this->proofBundle($review),
            'reuse_targets' => [
                'portfolio README update',
                'interview answer rehearsal',
                'next implementation challenge',
                'mentor review evidence',
                'weekly learning report',
            ],
            'retrieval_prompts' => $this->retrievalPrompts($technology, $review),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Save archive id',
                'Review retrieval keys',
                'Store proof bundle',
                'Choose reuse target',
                'Schedule next retrieval prompt',
            ]),
        ];
    }

    /**
     * Build search keys for finding the archive later.
     *
     * @return array<int, string>
     */
    private function retrievalKeys(string $technology, array $review): array
    {
        $keys = [
            sprintf('technology:%s', $technology),
            sprintf('checkpoint:%s', $review['checkpoint']['title']),
            sprintf('next-challenge:%s', $review['checkpoint']['next_challenge']['route']),
            'evidence:changed-files',
            'evidence:verification-commands',
        ];

        if ($technology === 'sql-injection-defense') {
            return [
                ...$keys,
                'security:sql-injection',
                'defense:parameterized-query',
                'defense:identifier-allowlist',
                'test:malicious-payloads',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                ...$keys,
                'security:csrf',
                'defense:csrf-token',
                'defense:samesite-cookie',
                'test:missing-or-stale-token',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                ...$keys,
                'security:xss',
                'defense:context-aware-escaping',
                'defense:sanitized-rich-text',
                'test:xss-payload-rendering',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                ...$keys,
                'security:misconfiguration',
                'config:production-readiness',
                'defense:fail-closed-smoke-checks',
                'risk:environment-drift',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                ...$keys,
                'security:idor',
                'defense:object-level-authorization',
                'defense:scoped-model-lookup',
                'test:cross-user-id-swap-denial',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                ...$keys,
                'security:oauth-pkce',
                'defense:s256-code-challenge',
                'defense:private-code-verifier',
                'test:callback-token-leakage',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                ...$keys,
                'algorithm:bfs-dfs',
                'graph:traversal-order',
                'defense:visited-set-cycle-safety',
                'system:api-database-hierarchy-guardrails',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisReview($review)) {
            return [
                ...$keys,
                'javascript:arrow-function-this',
                'runtime:lexical-this',
                'runtime:call-site-this',
                'interview:obj-arrow-bind-traps',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                ...$keys,
                'javascript:closure',
                'runtime:lexical-scope',
                'runtime:captured-binding',
                'interview:var-let-stale-closure',
            ];
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesReview($review)) {
            return [
                ...$keys,
                'php:stack-heap-memory',
                'runtime:call-frames',
                'runtime:heap-backed-data',
                'risk:long-running-worker-memory',
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesReview($review)) {
            return [
                ...$keys,
                'ai-agent:memory-contracts',
                'memory:working-episodic-semantic-procedural',
                'governance:source-freshness-confidence-permission',
                'test:stale-private-procedural-memory',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesReview($review)) {
            return [
                ...$keys,
                'ai:predictive-generative-comparison',
                'ai:output-contracts',
                'ai:evaluation-metrics',
                'risk:ai-failure-modes',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($review)) {
            return [
                ...$keys,
                'database:locking',
                'laravel:lockforupdate-transaction',
                'concurrency:deadlock-timeout-contention',
                'test:concurrent-write-invariant',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesReview($review)) {
            return [
                ...$keys,
                'database:covering-index',
                'postgres:index-only-scan',
                'postgres:heap-fetches',
                'ops:visibility-map-vacuum',
            ];
        }

        return $keys;
    }

    /**
     * Build durable proof references from review cards and criteria.
     *
     * @return array<int, array{label: string, detail: string}>
     */
    private function proofBundle(array $review): array
    {
        $cards = collect($review['cards'])
            ->map(fn (array $card): array => [
                'label' => $card['label'],
                'detail' => sprintf('%s Evidence: %s', $card['recall_prompt'], $card['evidence_recheck']),
            ])
            ->all();

        return [
            ...$cards,
            [
                'label' => 'Promotion criteria',
                'detail' => implode(' ', $review['promotion_criteria']),
            ],
        ];
    }

    /**
     * Build prompts used to retrieve the archive in later sessions.
     *
     * @return array<int, string>
     */
    private function retrievalPrompts(string $technology, array $review = []): array
    {
        if ($technology === 'sql-injection-defense') {
            return [
                'Explain SQL Injection from attacker input to changed query logic, then show how parameterized queries stop it.',
                'Name the changed files, binding examples, allowlist decisions, malicious payload tests, and verification commands.',
                'Reuse one proof item in an interview answer that compares escaping, bindings, and identifier allowlists.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'Explain CSRF from forged browser intent to unwanted state change, then show how session-bound tokens stop it.',
                'Name the changed files, token proof, SameSite decision, missing-token tests, and verification commands.',
                'Reuse one proof item in an interview answer that compares CSRF tokens, SameSite cookies, and safe HTTP methods.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'Explain XSS from untrusted input to executable browser context, then show how context-aware escaping stops it.',
                'Name the changed files, escaped output paths, sanitizer or raw-HTML decision, payload tests, and verification commands.',
                'Reuse one proof item in an interview answer that compares escaping, sanitization, safe JSON handoff, and CSP.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'Explain Security Misconfiguration from unsafe runtime setup to production exposure, then show how readiness checks stop release.',
                'Name the changed files, debug-mode check, secret-exposure review, CORS/header/cookie/proxy decisions, owners, and verification commands.',
                'Reuse one proof item in an interview answer that compares unsafe defaults, environment drift, boundary hardening, and fail-closed smoke checks.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'Explain IDOR as broken object-level authorization, then show how scoped lookup and policy checks stop ID swaps.',
                'Name the changed files, protected object routes, ownership or tenant boundary, denial tests, 403 or 404 decision, and verification commands.',
                'Reuse one proof item in an interview answer that compares authentication, authorization, route model binding, policies, and audit evidence.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'Explain PKCE from generated code_verifier to S256 code_challenge, then show why the verifier must stay private.',
                'Name the changed files, authorize URL evidence, callback validation, verifier failure tests, and verification commands.',
                'Reuse one proof item in an interview answer that compares Authorization Code with PKCE, Implicit Flow, and Client Credentials.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'Explain BFS versus DFS from traversal goal, then show how queue frontier differs from stack or recursion path.',
                'Name the changed files, traversal-order fixture, visited-set cycle test, API crawling guardrails, database hierarchy notes, and verification commands.',
                'Reuse one proof item in an interview answer that compares nearest-hop BFS, depth-first DFS, shortest unweighted path, memory pressure, and production limits.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisReview($review)) {
            return [
                'Explain arrow-function `this` as lexical this, then contrast it with normal function call-site this.',
                'Name the changed files, arrow-this comparison snippet, obj.arrow() output, call/apply/bind trap note, and verification commands.',
                'Reuse one proof item in an interview answer that compares arrow callbacks, normal object methods, obj.arrow() traps, and call/apply/bind limitations.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Explain JavaScript closure from lexical scope to captured binding, then trace createCounter() across repeated calls.',
                'Name the changed files, closure snippets, var versus let example, stale-closure note, and verification commands.',
                'Reuse one proof item in an interview answer that compares private state, callbacks, debounce, throttle, memoization, and React hook closures.',
            ];
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesReview($review)) {
            return [
                'Explain stack memory as active call frames, then contrast it with heap-backed arrays, objects, strings, and references.',
                'Name the changed files, cleanup notes, reference-counting or GC caveats, memory-risk example, and verification commands.',
                'Reuse one proof item in an interview answer that avoids manual-allocation claims and names PHP production memory risks.',
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesReview($review)) {
            return [
                'Explain AI agent memory as four separate contracts: working, episodic, semantic, and procedural memory.',
                'Name the changed files, source/freshness/confidence/permission metadata, retention rule, correction path, stale fallback, and private-memory blocking tests.',
                'Reuse one proof item in an interview answer that compares task state, session history, durable facts, reviewed playbooks, and why prompt history is not enough.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesReview($review)) {
            return [
                'Explain Predictive AI by output contract first: score, label, forecast, ranking, or risk decision.',
                'Explain Generative AI by output contract first: generated text, image, code, summary, answer, or multimodal content.',
                'Reuse one proof item in an interview answer that compares predictive metrics, generative checks, drift, hallucination, prompt injection, and unsafe generated code.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($review)) {
            return [
                'Explain database locking from protected invariant to concurrent write failure, then show how transaction-bound row locking stops it.',
                'Name the changed files, `DB::transaction()` boundary, `lockForUpdate()` lookup, deadlock or timeout behavior, contention notes, and verification commands.',
                'Reuse one proof item in an interview answer that compares row lock, table lock, deadlock, lock contention, indexed lookup, and short critical sections.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesReview($review)) {
            return [
                'Explain why an indexed query can still be slow when Index Scan performs many heap fetches.',
                'Name the changed files, INCLUDE column decision, EXPLAIN evidence, visibility-map or VACUUM note, and verification commands.',
                'Reuse one proof item in an interview answer that compares Index Scan, Index Only Scan, heap fetch cost, bloat risk, and write overhead.',
            ];
        }

        return [
            sprintf('Explain `%s` from source record to Laravel implementation without opening snippets.', $technology),
            'Name the changed files, verification commands, and review evidence.',
            'Choose one proof item and reuse it in a portfolio or interview answer.',
        ];
    }

    /**
     * Detect arrow-function `this` review content inside the broader JavaScript closure lane.
     */
    private function isArrowThisReview(array $review): bool
    {
        $haystack = strtolower((string) json_encode($review, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }
}
