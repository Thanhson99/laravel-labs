<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyMasteryCheckpointService
{
    /**
     * Create mastery checkpoints from technology remediation plans.
     */
    public function __construct(
        private readonly TechnologyRemediationPlanService $remediationPlans,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a promote-or-repeat checkpoint for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $remediation = $this->remediationPlans->build($technology, $filters);

        return [
            'title' => sprintf('Technology Mastery Checkpoint: %s', $technology),
            'technology' => $technology,
            'remediation_plan' => $remediation,
            'decision' => $this->decisionFor($technology, $remediation),
            'proof_checklist' => $this->proofChecklist($remediation),
            'next_challenge' => $this->nextChallengeFor($technology, $remediation),
            'handoff' => $this->handoffFor($technology, $remediation),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Confirm repair task evidence',
                'Run verification commands',
                'Make promote-or-repeat decision',
                'Choose next challenge',
                'Write next-session handoff',
            ]),
        ];
    }

    /**
     * Build proof items from remediation tasks and commands.
     *
     * @return array<int, string>
     */
    private function proofChecklist(array $remediation): array
    {
        $repairLabels = collect($remediation['tasks'])
            ->pluck('label')
            ->map(fn (string $label): string => sprintf('Evidence exists for `%s`.', $label))
            ->all();

        return [
            ...$repairLabels,
            sprintf('Verification commands were run: %s.', implode(' | ', $remediation['commands'])),
            'Next routes are known for implementation, interview defense, and reassessment.',
        ];
    }

    /**
     * Build promote-or-repeat rules for one technology.
     *
     * @return array{promote_when: string, repeat_when: string}
     */
    private function decisionFor(string $technology, array $remediation = []): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesRemediation($remediation)) {
            return [
                'promote_when' => 'Promote when the answer separates stack call frames from heap-backed arrays and objects, explains cleanup through scope and references, and names a PHP memory failure mode.',
                'repeat_when' => 'Repeat when the answer claims manual PHP stack or heap allocation, skips references or GC, lacks an example, or cannot name recursion, large-array, or worker-memory risk.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'promote_when' => 'Promote when unsafe SQL has been replaced with bindings, identifiers are allowlisted, payload tests pass, and the oral answer explains why input stayed data.',
                'repeat_when' => 'Repeat when the answer relies on escaping, misses dynamic identifiers, lacks malicious payload tests, or cannot point to the exact Laravel files.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'promote_when' => 'Promote when state-changing browser flows require token proof, SameSite behavior is documented, missing/stale token tests pass, and the oral answer explains forged browser intent.',
                'repeat_when' => 'Repeat when the answer treats SameSite as a full replacement for tokens, allows GET mutation, lacks 419/stale-token tests, or cannot point to the exact Laravel files.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'promote_when' => 'Promote when untrusted output is context-escaped, raw HTML is removed or sanitized, payload tests pass, and the oral answer explains reflected, stored, and DOM XSS.',
                'repeat_when' => 'Repeat when the answer relies on validation only, uses raw HTML without a trust boundary, lacks payload rendering tests, or cannot point to the exact Laravel files.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationRemediation($remediation)) {
            return [
                'promote_when' => 'Promote when the answer maps the full authentication lifecycle, proves throttling, session regeneration, reset-token expiry, logout invalidation, token revocation, and sensitive-log redaction with failure-path evidence.',
                'repeat_when' => 'Repeat when the answer only tests successful login, misses session fixation or stale reset tokens, lacks brute-force or revoked-token tests, logs sensitive identity secrets, or confuses authentication with authorization.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'promote_when' => 'Promote when unsafe defaults are detected, environment drift is documented, boundary settings are hardened, smoke checks fail closed, and the oral answer explains production readiness evidence.',
                'repeat_when' => 'Repeat when the answer only lists config names, misses exposed secrets or broad CORS, lacks smoke checks, skips owners or rollback notes, or cannot point to the exact Laravel files.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'promote_when' => 'Promote when object routes are inventoried, scoped lookups happen before object return, policies or Gates authorize exact actions, cross-user denial tests pass, and the oral answer explains broken object-level authorization.',
                'repeat_when' => 'Repeat when the answer only says "user is logged in", uses direct findOrFail() without scope or policy, misses nested/download/export paths, lacks ID-swap tests, or cannot point to the exact Laravel files.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'promote_when' => 'Promote when client type drives the flow choice, PKCE uses S256 with a private verifier, callback failure tests pass, and the oral answer explains token boundaries.',
                'repeat_when' => 'Repeat when the answer treats PKCE as optional, leaks code_verifier, accepts token fields in callback URLs, misses state validation, or cannot point to the exact Laravel files.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'promote_when' => 'Promote when the answer chooses BFS or DFS from the traversal goal, proves queue versus stack order, handles cycles, and names API or database guardrails with evidence.',
                'repeat_when' => 'Repeat when the answer says one traversal is always faster, skips visited-set or cycle safety, ignores depth or fan-out limits, or cannot connect traversal to API and database examples.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisRemediation($remediation)) {
            return [
                'promote_when' => 'Promote when the answer proves arrow functions use lexical `this`, contrasts normal function call-site `this`, explains obj.arrow() and call/apply/bind traps, and names a practical callback use case.',
                'repeat_when' => 'Repeat when the answer says arrow syntax is only shorter, claims obj.arrow() binds `this` to obj, skips call/apply/bind limitations, or cannot name when a normal method is safer.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'promote_when' => 'Promote when the answer traces lexical scope, captured bindings, repeated createCounter() calls, practical closure uses, var versus let, and stale-closure risk with evidence.',
                'repeat_when' => 'Repeat when the answer only says "inner function remembers variables", cannot trace the binding, skips var versus let, or cannot name stale closure and callback use cases.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesRemediation($remediation)) {
            return [
                'promote_when' => 'Promote when the answer separates Predictive AI and Generative AI by output contract, input evidence, metrics, and failure modes with one concrete product example for each.',
                'repeat_when' => 'Repeat when the answer treats all AI as generation, uses one generic accuracy or hallucination checklist, skips drift or prompt-injection risk, or cannot cite evidence.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($remediation)) {
            return [
                'promote_when' => 'Promote when the answer names the protected invariant, places `lockForUpdate()` inside `DB::transaction()`, proves the lookup is indexed, keeps the lock window short, and covers deadlock or timeout behavior.',
                'repeat_when' => 'Repeat when the answer says locks make writes safe without a transaction boundary, hides external work inside the lock, skips deadlock handling, or cannot prove concurrent requests fail closed.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesRemediation($remediation)) {
            return [
                'promote_when' => 'Promote when EXPLAIN evidence proves Heap Fetches dropped, INCLUDE columns are limited to the hot query, visibility-map health is addressed, and bloat or write overhead is documented.',
                'repeat_when' => 'Repeat when the answer only says "add an index", lacks before and after EXPLAIN output, ignores VACUUM or autovacuum, or cannot explain index size and rollback risk.',
            ];
        }

        return [
            'promote_when' => 'All repair tasks have evidence, verification commands pass, and the oral explanation names concrete Laravel files.',
            'repeat_when' => 'Any repair task still depends on vague explanation, missing tests, or unverified changed files.',
        ];
    }

    /**
     * Build the next challenge for one technology.
     *
     * @return array{label: string, route: string, success_signal: string}
     */
    private function nextChallengeFor(string $technology, array $remediation = []): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesRemediation($remediation)) {
            return [
                'label' => 'Build a second PHP runtime-memory example that contrasts a recursive call path with a large array or object graph.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next example separates call frames, heap-backed data, references, cleanup, and one production memory-risk signal.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'label' => 'Harden a second query path with raw SQL bindings, query builder filters, allowlisted sorting, and payload tests.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation rejects malicious payloads, keeps dynamic identifiers allowlisted, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'label' => 'Harden a second browser state-changing flow with CSRF token proof, SameSite review, method boundary checks, and stale-token tests.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation rejects missing or stale tokens, avoids GET mutation, documents SameSite behavior, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'label' => 'Harden a second rendering path with escaped output, safe JSON handoff, sanitizer review, CSP note, and payload tests.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation renders malicious payloads harmlessly, documents raw HTML decisions, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationRemediation($remediation)) {
            return [
                'label' => 'Harden a second authentication flow with throttling, session regeneration, reset-token expiry, logout invalidation, revocation, and redacted auth logging.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation proves unsafe auth states fail closed, separates authentication from authorization, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'label' => 'Harden a second deployment profile with debug-mode checks, secret exposure review, CORS/header boundaries, proxy trust, and release smoke tests.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation blocks unsafe production settings, documents owners and rollback actions, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'label' => 'Harden a second object route group with scoped lookup, policy authorization, nested-resource checks, and attacker ID-swap tests.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation denies cross-user or cross-tenant object access, documents 403 or 404 behavior, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'label' => 'Harden a second OAuth login flow with PKCE S256 proof, state validation, callback token-leakage rejection, and verifier failure tests.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation keeps code_verifier private, rejects bad callbacks, documents token boundaries, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'label' => 'Build a second traversal example that compares BFS nearest-hop behavior with DFS subtree validation.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next example proves traversal order, uses a visited set, documents cycle, depth, fan-out, pagination, rate-limit, and memory guardrails, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisRemediation($remediation)) {
            return [
                'label' => 'Build a second JavaScript arrow-this example that contrasts an object method with a timer or class callback.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next example proves lexical this, call-site this, obj.arrow() behavior, call/apply/bind limitations, and one case where a normal method is safer.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'label' => 'Build a second JavaScript closure example that contrasts private state with a stale async callback.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next example traces lexical scope, captured binding, var versus let behavior, stale closure risk, and practical callback use.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesRemediation($remediation)) {
            return [
                'label' => 'Build a second AI type comparison using one predictive scoring use case and one generative drafting use case.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next explanation separates output contract, input data, evaluation metrics, and failure controls for both AI types.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($remediation)) {
            return [
                'label' => 'Harden a second concurrent write path with transaction-bound row locking and deadlock-aware failure handling.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation proves the invariant under concurrent requests, keeps `lockForUpdate()` inside the transaction, documents indexed lookup and lock-wait risk, and can be defended in a two-minute interview answer.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesRemediation($remediation)) {
            return [
                'label' => 'Tune a second PostgreSQL hot query with EXPLAIN baseline, covering-index INCLUDE design, visibility-map check, and bloat guardrails.',
                'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'success_signal' => 'The next implementation proves lower Heap Fetches, keeps INCLUDE payload narrow, documents VACUUM expectations, and can be defended in a two-minute interview answer.',
            ];
        }

        return [
            'label' => sprintf('Build a harder %s source-backed implementation without starter snippets.', $technology),
            'route' => sprintf('/practice/technology-implementation-lab/%s', $technology),
            'success_signal' => 'The next implementation can be explained, tested, committed, and defended without reopening the generated code examples first.',
        ];
    }

    /**
     * Build next-session handoff guidance for one technology.
     *
     * @return array{next_session_goal: string, first_action: string, evidence_to_keep: string}
     */
    private function handoffFor(string $technology, array $remediation = []): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesRemediation($remediation)) {
            return [
                'next_session_goal' => 'Start with the weakest remaining PHP runtime-memory task: call frame, heap-backed data, cleanup, references, or failure mode.',
                'first_action' => 'Open the remediation plan, rerun the PHP focused test, and inspect one stack/heap explanation plus code example.',
                'evidence_to_keep' => 'Save the function example, large-array or object graph note, cleanup observation, and one concise stack-versus-heap interview answer.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'next_session_goal' => 'Start with the weakest remaining SQL Injection defense task: binding, identifier allowlist, payload test, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the SQL Injection focused test, and inspect one query construction path.',
                'evidence_to_keep' => 'Save changed files, payload test output, allowlist decision, and one concise parameterized-query explanation in the portfolio artifact.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'next_session_goal' => 'Start with the weakest remaining CSRF defense task: request method, token proof, SameSite review, stale-token test, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the CSRF focused test, and inspect one state-changing browser flow.',
                'evidence_to_keep' => 'Save changed files, 419 or stale-token test output, SameSite decision, and one concise CSRF explanation in the portfolio artifact.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'next_session_goal' => 'Start with the weakest remaining XSS defense task: variant explanation, context escaping, raw HTML boundary, payload test, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the XSS focused test, and inspect one browser rendering path.',
                'evidence_to_keep' => 'Save changed files, payload rendering test output, sanitizer or raw-HTML decision, and one concise XSS explanation in the portfolio artifact.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationRemediation($remediation)) {
            return [
                'next_session_goal' => 'Start with the weakest remaining Broken Authentication task: lifecycle model, session/reset control, failure-path test, sensitive auth logging, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the auth-security focused test, and inspect one login, reset, session, logout, or token revocation path.',
                'evidence_to_keep' => 'Save failure-path test output, session and reset-token control notes, revoked-token evidence, redacted auth logging fields, and one concise Broken Authentication explanation.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'next_session_goal' => 'Start with the weakest remaining Security Misconfiguration task: unsafe default, environment drift, boundary hardening, smoke check, owner, or rollback evidence.',
                'first_action' => 'Open the remediation plan, rerun the configuration readiness focused test, and inspect one production configuration boundary.',
                'evidence_to_keep' => 'Save changed files, failed-closed smoke check output, environment matrix, owner and rollback notes, and one concise Security Misconfiguration explanation.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'next_session_goal' => 'Start with the weakest remaining IDOR task: object inventory, scoped lookup, object policy, ID-swap denial test, status-code decision, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the IDOR focused test, and inspect one object route plus its lookup and authorization path.',
                'evidence_to_keep' => 'Save changed files, cross-user denial output, route inventory, 403 or 404 decision, monitoring note, and one concise IDOR explanation in the portfolio artifact.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'next_session_goal' => 'Start with the weakest remaining OAuth task: flow fit, PKCE proof, callback validation, token boundary, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the OAuth focused test, and inspect one authorize URL plus callback handling path.',
                'evidence_to_keep' => 'Save changed files, verifier failure test output, callback leakage decision, and one concise PKCE explanation in the portfolio artifact.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'next_session_goal' => 'Start with the weakest remaining BFS/DFS task: traversal goal, state model, cycle safety, system fit, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the graph traversal focused test, and inspect one BFS queue example plus one DFS stack or recursion example.',
                'evidence_to_keep' => 'Save traversal-order output, visited-set and cycle test evidence, API crawling guardrails, database hierarchy notes, and one concise BFS-versus-DFS interview answer.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisRemediation($remediation)) {
            return [
                'next_session_goal' => 'Start with the weakest remaining arrow-this task: lexical this, call-site comparison, obj.arrow() trap, call/apply/bind limitation, callback use, or interview caveat.',
                'first_action' => 'Open the remediation plan, rerun the arrow-this focused tests, and inspect one normal method versus arrow property example.',
                'evidence_to_keep' => 'Save the arrow-this comparison snippet, obj.arrow() output, call/apply/bind trap note, callback use case, and one concise interview answer.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'next_session_goal' => 'Start with the weakest remaining closure task: lexical scope, captured binding, practical usage, var versus let, stale closure, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the closure focused tests, and inspect one createCounter() example plus one stale-closure note.',
                'evidence_to_keep' => 'Save the closure snippet, repeated-call output trace, var versus let explanation, stale-closure note, and one concise interview answer.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesRemediation($remediation)) {
            return [
                'next_session_goal' => 'Start with the weakest remaining AI comparison task: output contract, input evidence, evaluation fit, or failure modes.',
                'first_action' => 'Open the remediation plan, rerun the LLM focused test, and inspect one Predictive AI example plus one Generative AI example.',
                'evidence_to_keep' => 'Save the comparison table, metric choices, generation quality checks, failure-mode notes, and one concise interview answer.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($remediation)) {
            return [
                'next_session_goal' => 'Start with the weakest remaining database-locking task: protected invariant, transaction boundary, lock scope, deadlock behavior, contention, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the database locking focused test, and inspect one transaction that reads, validates, locks, and writes a protected row.',
                'evidence_to_keep' => 'Save concurrent-request evidence, invariant note, `DB::transaction()` and `lockForUpdate()` snippet, indexed lookup review, deadlock or timeout behavior, and lock-wait monitoring note.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesRemediation($remediation)) {
            return [
                'next_session_goal' => 'Start with the weakest remaining covering-index task: plan baseline, INCLUDE design, visibility health, cost guardrails, or interview explanation.',
                'first_action' => 'Open the remediation plan, rerun the database focused test, and inspect one EXPLAIN plan plus one covering-index migration note.',
                'evidence_to_keep' => 'Save before and after EXPLAIN output, Heap Fetches count, INCLUDE column rationale, VACUUM or autovacuum note, bloat estimate, and rollback command.',
            ];
        }

        return [
            'next_session_goal' => sprintf('Start with the weakest remaining `%s` remediation task.', $technology),
            'first_action' => 'Open the remediation plan and rerun the first verification command.',
            'evidence_to_keep' => 'Save changed files, command output, and one concise explanation in the portfolio artifact.',
        ];
    }

    /**
     * Detect arrow-function `this` work inside the broader JavaScript closure lane.
     */
    private function isArrowThisRemediation(array $remediation): bool
    {
        $haystack = strtolower((string) json_encode($remediation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect broken-authentication work inside the broader auth-security lane.
     */
    private function isBrokenAuthenticationRemediation(array $remediation): bool
    {
        $haystack = strtolower((string) json_encode($remediation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }
}
