<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyMasteryCheckpointTest extends TestCase
{
    /**
     * The mastery checkpoint page renders decision rules, proof, and handoff.
     */
    public function test_technology_mastery_checkpoint_page_renders_decision_and_handoff(): void
    {
        $response = $this->get('/practice/technology-mastery-checkpoint/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Mastery Checkpoint: api-validation')
            ->assertSee('Proof Checklist')
            ->assertSee('Next Challenge')
            ->assertSee('Next Session Handoff')
            ->assertSee('Open checkpoint API');
    }

    /**
     * The mastery checkpoint API returns proof checklist, next challenge, and progress payload.
     */
    public function test_technology_mastery_checkpoint_api_returns_checkpoint_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-mastery-checkpoint/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.next_challenge.route', '/practice/technology-implementation-lab/api-validation')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Confirm repair task evidence')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'remediation_plan',
                    'decision',
                    'proof_checklist',
                    'next_challenge',
                    'handoff',
                    'progress_payload',
                ],
            ]);
    }

    /**
     * SQL Injection mastery checkpoints promote only with binding and payload-test evidence.
     */
    public function test_sql_injection_mastery_checkpoint_uses_security_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.decision.promote_when', 'Promote when unsafe SQL has been replaced with bindings, identifiers are allowlisted, payload tests pass, and the oral answer explains why input stayed data.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second query path with raw SQL bindings, query builder filters, allowlisted sorting, and payload tests.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the SQL Injection focused test, and inspect one query construction path.');
    }

    /**
     * CSRF mastery checkpoints promote only with token, SameSite, and stale-token evidence.
     */
    public function test_csrf_mastery_checkpoint_uses_security_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.decision.promote_when', 'Promote when state-changing browser flows require token proof, SameSite behavior is documented, missing/stale token tests pass, and the oral answer explains forged browser intent.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second browser state-changing flow with CSRF token proof, SameSite review, method boundary checks, and stale-token tests.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the CSRF focused test, and inspect one state-changing browser flow.');
    }

    /**
     * XSS mastery checkpoints promote only with escaping, sanitization, and payload-test evidence.
     */
    public function test_xss_mastery_checkpoint_uses_security_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.decision.promote_when', 'Promote when untrusted output is context-escaped, raw HTML is removed or sanitized, payload tests pass, and the oral answer explains reflected, stored, and DOM XSS.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second rendering path with escaped output, safe JSON handoff, sanitizer review, CSP note, and payload tests.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the XSS focused test, and inspect one browser rendering path.');
    }

    /**
     * Security Misconfiguration mastery checkpoints promote only with readiness and smoke-check evidence.
     */
    public function test_security_misconfiguration_mastery_checkpoint_uses_configuration_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.decision.promote_when', 'Promote when unsafe defaults are detected, environment drift is documented, boundary settings are hardened, smoke checks fail closed, and the oral answer explains production readiness evidence.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer only lists config names, misses exposed secrets or broad CORS, lacks smoke checks, skips owners or rollback notes, or cannot point to the exact Laravel files.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second deployment profile with debug-mode checks, secret exposure review, CORS/header boundaries, proxy trust, and release smoke tests.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the configuration readiness focused test, and inspect one production configuration boundary.');
    }

    /**
     * IDOR mastery checkpoints promote only with scoped lookup, object policies, and denial evidence.
     */
    public function test_idor_mastery_checkpoint_uses_object_authorization_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.decision.promote_when', 'Promote when object routes are inventoried, scoped lookups happen before object return, policies or Gates authorize exact actions, cross-user denial tests pass, and the oral answer explains broken object-level authorization.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer only says "user is logged in", uses direct findOrFail() without scope or policy, misses nested/download/export paths, lacks ID-swap tests, or cannot point to the exact Laravel files.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second object route group with scoped lookup, policy authorization, nested-resource checks, and attacker ID-swap tests.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the IDOR focused test, and inspect one object route plus its lookup and authorization path.');
    }

    /**
     * Broken Authentication mastery checkpoints promote only with lifecycle and failure-path evidence.
     */
    public function test_broken_authentication_mastery_checkpoint_uses_lifecycle_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer maps the full authentication lifecycle, proves throttling, session regeneration, reset-token expiry, logout invalidation, token revocation, and sensitive-log redaction with failure-path evidence.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer only tests successful login, misses session fixation or stale reset tokens, lacks brute-force or revoked-token tests, logs sensitive identity secrets, or confuses authentication with authorization.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second authentication flow with throttling, session regeneration, reset-token expiry, logout invalidation, revocation, and redacted auth logging.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the auth-security focused test, and inspect one login, reset, session, logout, or token revocation path.');
    }

    /**
     * OAuth mastery checkpoints promote only with PKCE proof, callback tests, and token-boundary evidence.
     */
    public function test_oauth_mastery_checkpoint_uses_pkce_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.decision.promote_when', 'Promote when client type drives the flow choice, PKCE uses S256 with a private verifier, callback failure tests pass, and the oral answer explains token boundaries.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second OAuth login flow with PKCE S256 proof, state validation, callback token-leakage rejection, and verifier failure tests.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the OAuth focused test, and inspect one authorize URL plus callback handling path.');
    }

    /**
     * Graph traversal mastery checkpoints promote only with BFS/DFS goal, state, and guardrail evidence.
     */
    public function test_graph_traversal_mastery_checkpoint_uses_bfs_dfs_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer chooses BFS or DFS from the traversal goal, proves queue versus stack order, handles cycles, and names API or database guardrails with evidence.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer says one traversal is always faster, skips visited-set or cycle safety, ignores depth or fan-out limits, or cannot connect traversal to API and database examples.')
            ->assertJsonPath('data.next_challenge.label', 'Build a second traversal example that compares BFS nearest-hop behavior with DFS subtree validation.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the graph traversal focused test, and inspect one BFS queue example plus one DFS stack or recursion example.');
    }

    /**
     * JavaScript closure mastery checkpoints promote only with lexical-scope and trap evidence.
     */
    public function test_javascript_closure_mastery_checkpoint_uses_scope_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer traces lexical scope, captured bindings, repeated createCounter() calls, practical closure uses, var versus let, and stale-closure risk with evidence.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer only says "inner function remembers variables", cannot trace the binding, skips var versus let, or cannot name stale closure and callback use cases.')
            ->assertJsonPath('data.next_challenge.label', 'Build a second JavaScript closure example that contrasts private state with a stale async callback.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the closure focused tests, and inspect one createCounter() example plus one stale-closure note.');
    }

    /**
     * Arrow-function this mastery checkpoints promote only with lexical-this and binding-trap evidence.
     */
    public function test_arrow_this_mastery_checkpoint_uses_lexical_this_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer proves arrow functions use lexical `this`, contrasts normal function call-site `this`, explains obj.arrow() and call/apply/bind traps, and names a practical callback use case.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer says arrow syntax is only shorter, claims obj.arrow() binds `this` to obj, skips call/apply/bind limitations, or cannot name when a normal method is safer.')
            ->assertJsonPath('data.next_challenge.label', 'Build a second JavaScript arrow-this example that contrasts an object method with a timer or class callback.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the arrow-this focused tests, and inspect one normal method versus arrow property example.');
    }

    /**
     * PHP stack and heap mastery checkpoints promote only with runtime-memory evidence.
     */
    public function test_php_stack_heap_mastery_checkpoint_uses_runtime_memory_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer separates stack call frames from heap-backed arrays and objects, explains cleanup through scope and references, and names a PHP memory failure mode.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer claims manual PHP stack or heap allocation, skips references or GC, lacks an example, or cannot name recursion, large-array, or worker-memory risk.')
            ->assertJsonPath('data.next_challenge.label', 'Build a second PHP runtime-memory example that contrasts a recursive call path with a large array or object graph.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the PHP focused test, and inspect one stack/heap explanation plus code example.');
    }

    /**
     * AI type comparison mastery checkpoints promote only with separated contracts and evidence.
     */
    public function test_predictive_generative_ai_mastery_checkpoint_uses_ai_type_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer separates Predictive AI and Generative AI by output contract, input evidence, metrics, and failure modes with one concrete product example for each.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer treats all AI as generation, uses one generic accuracy or hallucination checklist, skips drift or prompt-injection risk, or cannot cite evidence.')
            ->assertJsonPath('data.next_challenge.label', 'Build a second AI type comparison using one predictive scoring use case and one generative drafting use case.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the LLM focused test, and inspect one Predictive AI example plus one Generative AI example.');
    }

    /**
     * Covering Index mastery checkpoints promote only with query-plan and operations evidence.
     */
    public function test_covering_index_mastery_checkpoint_uses_query_plan_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.decision.promote_when', 'Promote when EXPLAIN evidence proves Heap Fetches dropped, INCLUDE columns are limited to the hot query, visibility-map health is addressed, and bloat or write overhead is documented.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer only says "add an index", lacks before and after EXPLAIN output, ignores VACUUM or autovacuum, or cannot explain index size and rollback risk.')
            ->assertJsonPath('data.next_challenge.label', 'Tune a second PostgreSQL hot query with EXPLAIN baseline, covering-index INCLUDE design, visibility-map check, and bloat guardrails.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the database focused test, and inspect one EXPLAIN plan plus one covering-index migration note.');
    }

    /**
     * Database locking mastery checkpoints promote only with transaction and contention evidence.
     */
    public function test_database_locking_mastery_checkpoint_uses_concurrency_decision_rules(): void
    {
        $this->getJson('/api/practice/technology-mastery-checkpoint/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.decision.promote_when', 'Promote when the answer names the protected invariant, places `lockForUpdate()` inside `DB::transaction()`, proves the lookup is indexed, keeps the lock window short, and covers deadlock or timeout behavior.')
            ->assertJsonPath('data.decision.repeat_when', 'Repeat when the answer says locks make writes safe without a transaction boundary, hides external work inside the lock, skips deadlock handling, or cannot prove concurrent requests fail closed.')
            ->assertJsonPath('data.next_challenge.label', 'Harden a second concurrent write path with transaction-bound row locking and deadlock-aware failure handling.')
            ->assertJsonPath('data.handoff.first_action', 'Open the remediation plan, rerun the database locking focused test, and inspect one transaction that reads, validates, locks, and writes a protected row.');
    }
}
