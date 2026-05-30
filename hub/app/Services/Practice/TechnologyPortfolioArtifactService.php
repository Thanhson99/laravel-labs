<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyPortfolioArtifactService
{
    /**
     * Create portfolio-ready artifacts from technology commit plans.
     */
    public function __construct(
        private readonly TechnologyCommitPlanService $commitPlans,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a portfolio entry for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $commitPlan = $this->commitPlans->build($technology, $filters);
        $sourceItems = $commitPlan['lab']['source_examples']['items'];

        return [
            'title' => sprintf('Technology Portfolio Artifact: %s', $technology),
            'technology' => $technology,
            'commit_plan' => $commitPlan,
            'portfolio' => [
                'headline' => $this->headlineFor($technology, $sourceItems),
                'summary' => $this->summaryFor($technology, $sourceItems),
                'source_coverage' => $this->sourceCoverage($sourceItems),
                'changed_files' => $commitPlan['changed_files'],
                'verification' => $commitPlan['verification'],
                'interview_talking_points' => $this->talkingPoints($technology, $sourceItems),
                'readme_template' => $this->readmeTemplate($technology, $commitPlan),
            ],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Write portfolio headline',
                'List source records covered',
                'Attach changed files and verification commands',
                'Prepare interview talking points',
                'Save README-style artifact',
            ]),
        ];
    }

    /**
     * Summarize what the learner implemented from source records.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     * @return array<int, string>
     */
    private function summaryFor(string $technology, array $sourceItems): array
    {
        if ($technology === 'react-render-performance') {
            return [
                'Built a React render optimization practice artifact around measurement-first performance work.',
                sprintf('Mapped %d JSON content records into profiler, memoization, and review tasks.', count($sourceItems)),
                'Separated React.memo, useMemo, useCallback, state locality, and virtualization decisions.',
                'Connected the Hub workbench, API planner, tests, and verification commands into one reusable artifact.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisSource($sourceItems)) {
            return [
                'Built a JavaScript arrow-function this artifact around lexical this and call-site comparison.',
                sprintf('Mapped %d JSON content records into arrow-this examples, object-method traps, and interview tasks.', count($sourceItems)),
                'Separated arrow lexical this, normal function dynamic this, obj.arrow() traps, call/apply/bind limits, and callback use cases.',
                'Connected the Hub workbench, implementation lab, quality plan, and interview prompts into one reusable frontend concept artifact.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Built a JavaScript closure interview artifact around lexical scope and captured bindings.',
                sprintf('Mapped %d JSON content records into closure examples, scope tracing, and interview-trap tasks.', count($sourceItems)),
                'Separated lexical scope, captured variable binding, private state, var versus let loops, stale closures, and practical callback use.',
                'Connected the Hub workbench, implementation lab, quality plan, and interview prompts into one reusable frontend concept artifact.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'Built a SQL Injection defense artifact around parameterized queries and boundary review.',
                sprintf('Mapped %d JSON content records into binding, allowlist, and attack-payload test tasks.', count($sourceItems)),
                'Separated value binding from dynamic identifier allowlisting.',
                'Connected the Hub workbench, API planner, tests, and verification commands into one reusable security artifact.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'Built a CSRF protection artifact around browser intent, token proof, and cookie boundaries.',
                sprintf('Mapped %d JSON content records into CSRF token, SameSite, and failure-test tasks.', count($sourceItems)),
                'Separated CSRF token validation from SameSite cookie behavior.',
                'Connected the Hub workbench, API planner, tests, and verification commands into one reusable security artifact.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'Built an XSS defense artifact around context-aware escaping and safe browser rendering.',
                sprintf('Mapped %d JSON content records into reflected, stored, DOM, sanitizer, and payload-test tasks.', count($sourceItems)),
                'Separated escaped text, sanitized rich text, safe JavaScript handoff, and CSP defense-in-depth.',
                'Connected the Hub workbench, API planner, tests, and verification commands into one reusable security artifact.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationSource($sourceItems)) {
            return [
                'Built a Broken Authentication artifact around authentication lifecycle hardening.',
                sprintf('Mapped %d JSON content records into login, session, reset-token, logout, revocation, and logging tasks.', count($sourceItems)),
                'Separated identity proof, session lifecycle, password reset safety, token revocation, remember-me, MFA recovery, and authorization boundaries.',
                'Connected the Hub workbench, implementation lab, quality plan, commit plan, and interview prompts into one reusable authentication security artifact.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'Built a Security Misconfiguration artifact around production configuration readiness.',
                sprintf('Mapped %d JSON content records into debug-mode, secret-exposure, CORS, header, proxy, storage, and smoke-check tasks.', count($sourceItems)),
                'Separated unsafe defaults, environment drift, boundary hardening, deployment smoke checks, owners, and rollback evidence.',
                'Connected the Hub configuration readiness lab, implementation lab, quality plan, and interview prompts into one reusable security artifact.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'Built an IDOR defense artifact around object-level authorization and scoped lookup.',
                sprintf('Mapped %d JSON content records into route inventory, ownership scope, policy, denial-test, and monitoring tasks.', count($sourceItems)),
                'Separated authentication, object authorization, route model binding, tenant scope, nested-resource checks, and 403 versus 404 decisions.',
                'Connected the Hub IDOR workbench, implementation lab, quality plan, and interview prompts into one reusable API security artifact.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'Built an OAuth PKCE artifact around public-client login proof and callback validation.',
                sprintf('Mapped %d JSON content records into flow selection, verifier lifecycle, and token-boundary tasks.', count($sourceItems)),
                'Separated Authorization Code with PKCE, Implicit Flow, and Client Credentials by client type and trust boundary.',
                'Connected the Hub workbench, API planner, tests, and verification commands into one reusable OAuth security artifact.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'Built a BFS versus DFS graph traversal artifact around goal-first algorithm choice.',
                sprintf('Mapped %d JSON content records into traversal goal, queue, stack, cycle-safety, and system-guardrail tasks.', count($sourceItems)),
                'Separated BFS nearest-hop and shortest unweighted path behavior from DFS branch exploration and subtree validation behavior.',
                'Connected the Hub workbench, implementation lab, quality plan, and interview prompts into one reusable algorithms-in-systems artifact.',
            ];
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesSourceItems($sourceItems)) {
            return [
                'Built a PHP runtime-memory artifact around stack call frames and heap-backed data.',
                sprintf('Mapped %d JSON content records into call-frame, heap-pressure, cleanup, and failure-mode tasks.', count($sourceItems)),
                'Separated PHP stack mental model, arrays, objects, strings, references, reference counting, and garbage collection.',
                'Connected the Hub workbench, implementation lab, tests, and interview prompts into one reusable PHP memory artifact.',
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesSourceItems($sourceItems)) {
            return [
                'Built an AI agent memory artifact around working, episodic, semantic, and procedural memory contracts.',
                sprintf('Mapped %d JSON content records into memory boundaries, governance metadata, stale-memory checks, and private-memory safeguards.', count($sourceItems)),
                'Separated short-lived task context, session history, durable facts, and reviewed playbooks before discussing storage.',
                'Connected the Hub agent-memory workbench, implementation lab, quality plan, commit evidence, and interview prompts into one reusable AI engineering artifact.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesSourceItems($sourceItems)) {
            return [
                'Built an AI type comparison artifact around Predictive AI and Generative AI output contracts.',
                sprintf('Mapped %d JSON content records into prediction, generation, evaluation, and failure-mode tasks.', count($sourceItems)),
                'Separated scores, labels, forecasts, and rankings from generated text, code, images, summaries, and answers.',
                'Connected the Hub workbench, implementation lab, quality plan, and interview prompts into one reusable AI explanation artifact.',
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                'Built an AI chatbot context-strategy artifact around RAG, Long Context, CAG, and hybrid routing.',
                sprintf('Mapped %d JSON content records into context selection, answer contracts, router logic, guardrails, and evaluation tasks.', count($sourceItems)),
                'Separated retrieval-backed RAG, packed-document Long Context, cache-backed CAG, and hybrid route selection by freshness, corpus size, permissions, latency, and cost.',
                'Connected the Hub RAG strategy workbench, implementation lab, quality plan, commit evidence, and interview prompts into one reusable chatbot architecture artifact.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['source_items' => $sourceItems])) {
            return [
                'Built a database locking artifact around transaction-safe concurrency control.',
                sprintf('Mapped %d JSON content records into row-lock, transaction-boundary, deadlock, and contention-review tasks.', count($sourceItems)),
                'Separated protected invariants, `DB::transaction()`, `lockForUpdate()`, indexed lookups, lock waits, and retry behavior.',
                'Connected the Hub implementation lab, quality plan, commit evidence, and interview prompts into one reusable database concurrency artifact.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['source_items' => $sourceItems])) {
            return [
                'Built a PostgreSQL covering-index artifact around heap-fetch reduction and Index Only Scan evidence.',
                sprintf('Mapped %d JSON content records into EXPLAIN baseline, INCLUDE design, visibility-map, and bloat-guardrail tasks.', count($sourceItems)),
                'Separated index key columns, included payload columns, visibility map health, VACUUM expectations, and write-overhead tradeoffs.',
                'Connected the Hub workbench, implementation lab, quality plan, and interview prompts into one reusable database performance artifact.',
            ];
        }

        return [
            sprintf('Built a Laravel practice slice focused on `%s`.', $technology),
            sprintf('Mapped %d JSON content records into code-first implementation tasks.', count($sourceItems)),
            'Kept source content read-only while implementing behavior in Laravel code.',
            'Connected snippets, workspaces, verification commands, and commit evidence into one learning artifact.',
        ];
    }

    /**
     * Build source coverage rows for the portfolio artifact.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     * @return array<int, array{record_id: string, source_path: string, title: string, task: string}>
     */
    private function sourceCoverage(array $sourceItems): array
    {
        return collect($sourceItems)
            ->map(fn (array $item): array => [
                'record_id' => (string) $item['record_id'],
                'source_path' => (string) $item['source']['path'],
                'title' => (string) $item['content']['title'],
                'task' => (string) $item['task'],
            ])
            ->values()
            ->all();
    }

    /**
     * Build interview talking points from source-backed implementation work.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     * @return array<int, string>
     */
    private function talkingPoints(string $technology, array $sourceItems): array
    {
        $sampleTitle = (string) ($sourceItems[0]['content']['title'] ?? 'the selected content record');

        if ($technology === 'react-render-performance') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a React render optimization workflow.', $sampleTitle),
                'I start from React Profiler evidence instead of assuming every render is a bug.',
                'I can explain when React.memo, useMemo, useCallback, state locality, or virtualization is the right lever.',
                'I can show the workbench route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisSource($sourceItems)) {
            return [
                sprintf('I used `%s` as the source topic and turned it into a JavaScript arrow-this workflow.', $sampleTitle),
                'I explain that arrow functions do not create their own `this`; they read `this` from the lexical scope where they are created.',
                'I can separate normal function call-site `this`, arrow lexical `this`, obj.arrow() traps, and call/apply/bind limitations.',
                'I can show the source record, arrow-this snippets, implementation lab, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a JavaScript closure workflow.', $sampleTitle),
                'I explain closure as a function keeping access to variables from the lexical scope where it was created.',
                'I can separate captured variable bindings, private state, var versus let loop behavior, stale closures, and callback use cases.',
                'I can show the source record, closure snippets, implementation lab, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a SQL Injection defense workflow.', $sampleTitle),
                'I explain SQL Injection as user input crossing a boundary and becoming SQL logic.',
                'I can separate parameterized values from allowlisted identifiers such as sort columns.',
                'I can show the workbench route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a CSRF protection workflow.', $sampleTitle),
                'I explain CSRF as browser intent being forged while cookies are sent automatically.',
                'I can separate token proof, SameSite cookie behavior, safe method boundaries, and stale-token tests.',
                'I can show the workbench route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                sprintf('I used `%s` as the source topic and turned it into an XSS defense workflow.', $sampleTitle),
                'I explain XSS as untrusted data reaching an executable browser context.',
                'I can separate escaped Blade output, safe JSON handoff, sanitizer boundaries, CSP, and payload tests.',
                'I can show the workbench route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationSource($sourceItems)) {
            return [
                sprintf('I used `%s` as the source topic and turned it into a Broken Authentication lifecycle workflow.', $sampleTitle),
                'I explain broken authentication as any weakness that lets the wrong person become, stay, or act as a user.',
                'I can separate login throttling, session regeneration, reset-token expiry, logout invalidation, token revocation, remember-me, MFA recovery, and sensitive-log redaction.',
                'I can show the workbench route, implementation lab, quality plan, commit evidence, interview pack, and verification commands.',
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a Security Misconfiguration readiness workflow.', $sampleTitle),
                'I explain misconfiguration as unsafe runtime or deployment setup that exposes data or weakens defenses.',
                'I can separate debug mode, secret exposure, public storage, CORS, security headers, cookie flags, trusted proxies, and environment drift.',
                'I can show the configuration readiness route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                sprintf('I used `%s` as the source topic and turned it into an IDOR object-authorization workflow.', $sampleTitle),
                'I explain IDOR as broken object-level authorization where a valid user changes an object identifier to access someone else data.',
                'I can separate authentication, owner or tenant-scoped lookup, Policy or Gate checks, nested-resource boundaries, and 403 versus 404 decisions.',
                'I can show the workbench route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                sprintf('I used `%s` as the source topic and turned it into an OAuth PKCE workflow.', $sampleTitle),
                'I explain PKCE as proof that the client exchanging the authorization code is the client that started the login.',
                'I can separate public-client PKCE, confidential-client secrets, Client Credentials, callback validation, and token storage boundaries.',
                'I can show the workbench route, API route, service, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a BFS versus DFS workflow.', $sampleTitle),
                'I explain BFS and DFS as traversal choices driven by goal, graph shape, and production constraints.',
                'I can separate queue frontier, stack or recursion path, shortest unweighted path, visited set, cycle safety, and memory tradeoffs.',
                'I can show API crawling and database hierarchy guardrails instead of treating algorithms as interview-only theory.',
            ];
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesSourceItems($sourceItems)) {
            return [
                sprintf('I used `%s` as the source topic and turned it into a PHP runtime-memory workflow.', $sampleTitle),
                'I explain stack memory as active call frames and heap memory as backing larger arrays, objects, strings, references, and object graphs.',
                'I can separate scope exit, unset(), reference counting, garbage collection, and long-running worker memory risk.',
                'I can show the source record, workbench route, implementation lab, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesSourceItems($sourceItems)) {
            return [
                sprintf('I used `%s` as the source topic and turned it into an AI agent memory workflow.', $sampleTitle),
                'I explain working, episodic, semantic, and procedural memory as separate contracts instead of one prompt-history bucket.',
                'I can separate freshness, confidence, source, permission, retention, correction path, stale-memory fallback, and private-memory blocking.',
                'I can show the agent-memory workbench, contract table, failure tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesSourceItems($sourceItems)) {
            return [
                sprintf('I used `%s` as the source topic and turned it into a Predictive AI versus Generative AI workflow.', $sampleTitle),
                'I explain Predictive AI as score, label, forecast, ranking, or risk-decision output from existing data.',
                'I explain Generative AI as new text, image, code, summary, answer, or multimodal output from prompt and context.',
                'I can show separate metrics and failure modes instead of using one generic AI quality checklist.',
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                sprintf('I used `%s` as the source topic and turned it into a chatbot context strategy workflow.', $sampleTitle),
                'I explain RAG, Long Context, CAG, and hybrid routing as different ways to supply trustworthy context to an AI chatbot.',
                'I can separate retrieval freshness, packed-document token budgets, cache versioning, tenant permissions, citations, fallback behavior, and cost or latency tradeoffs.',
                'I can show the RAG strategy workbench, context router, answer contract, tests, review checklist, and verification commands.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['source_items' => $sourceItems])) {
            return [
                sprintf('I used `%s` as the source topic and turned it into a database locking workflow.', $sampleTitle),
                'I explain database locking as the mechanism that stops concurrent requests from reading and changing the same critical row unsafely.',
                'I can separate transaction boundary, row lock, table lock, deadlock, lock contention, retry policy, and indexed lookup risk.',
                'I can show `lockForUpdate()` evidence tied to an invariant such as no oversold inventory or no double-spent balance.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['source_items' => $sourceItems])) {
            return [
                sprintf('I used `%s` as the source topic and turned it into a PostgreSQL covering-index workflow.', $sampleTitle),
                'I explain heap fetch as the extra table-read step that can keep an indexed query slow.',
                'I can separate key columns, INCLUDE payload columns, Index Only Scan, visibility map, VACUUM, bloat risk, write overhead, and rollback evidence.',
                'I can show before and after EXPLAIN evidence instead of claiming that adding an index is automatically faster.',
            ];
        }

        return [
            sprintf('I can explain how `%s` appears in a real Laravel code path, not only as theory.', $technology),
            sprintf('I used `%s` as the source record and turned it into concrete files, tests, and verification.', $sampleTitle),
            'I separated route mapping, controller orchestration, service behavior, validation, and verification evidence.',
            'I can show the branch, changed files, commands, and review checklist that prove the work is traceable.',
        ];
    }

    /**
     * Create a reusable README-style portfolio template.
     */
    private function readmeTemplate(string $technology, array $commitPlan): string
    {
        if ($technology === 'react-render-performance') {
            return $this->reactRenderReadmeTemplate($commitPlan);
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisSource($commitPlan['lab']['source_examples']['items'] ?? [])) {
            return $this->javascriptArrowThisReadmeTemplate($commitPlan);
        }

        if ($technology === 'javascript-closures') {
            return $this->javascriptClosureReadmeTemplate($commitPlan);
        }

        if ($technology === 'sql-injection-defense') {
            return $this->sqlInjectionReadmeTemplate($commitPlan);
        }

        if ($technology === 'csrf-protection') {
            return $this->csrfProtectionReadmeTemplate($commitPlan);
        }

        if ($technology === 'xss-defense') {
            return $this->xssDefenseReadmeTemplate($commitPlan);
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationSource($commitPlan['lab']['source_examples']['items'] ?? [])) {
            return $this->brokenAuthenticationReadmeTemplate($commitPlan);
        }

        if ($technology === 'security-misconfiguration') {
            return $this->securityMisconfigurationReadmeTemplate($commitPlan);
        }

        if ($technology === 'idor-access-control') {
            return $this->idorAccessControlReadmeTemplate($commitPlan);
        }

        if ($technology === 'oauth-flow') {
            return $this->oauthFlowReadmeTemplate($commitPlan);
        }

        if ($technology === 'graph-traversal') {
            return $this->graphTraversalReadmeTemplate($commitPlan);
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesSourceItems($commitPlan['lab']['source_examples']['items'] ?? [])) {
            return $this->phpRuntimeMemoryReadmeTemplate($commitPlan);
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesSourceItems($commitPlan['lab']['source_examples']['items'] ?? [])) {
            return $this->aiAgentMemoryReadmeTemplate($commitPlan);
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesSourceItems($commitPlan['lab']['source_examples']['items'] ?? [])) {
            return $this->aiTypeComparisonReadmeTemplate($commitPlan);
        }

        if ($technology === 'rag-systems') {
            return $this->ragContextStrategyReadmeTemplate($commitPlan);
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($commitPlan)) {
            return $this->databaseLockingReadmeTemplate($commitPlan);
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['source_items' => $commitPlan['lab']['source_examples']['items'] ?? []])) {
            return $this->coveringIndexReadmeTemplate($commitPlan);
        }

        $title = Str::headline($technology);
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# {$title} Practice Artifact

## Goal
Implement a content-backed Laravel practice slice for `{$technology}`.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Source records were read from JSON content.
- Laravel code owns the implementation.
- Tests and route checks prove the behavior.
MARKDOWN;
    }

    /**
     * Build a concise headline for the portfolio artifact.
     */
    private function headlineFor(string $technology, array $sourceItems = []): string
    {
        if ($technology === 'react-render-performance') {
            return 'Built a measurement-first React render optimization workbench';
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisSource($sourceItems)) {
            return 'Built a JavaScript arrow-function lexical-this interview artifact';
        }

        if ($technology === 'javascript-closures') {
            return 'Built a JavaScript closure lexical-scope interview artifact';
        }

        if ($technology === 'sql-injection-defense') {
            return 'Built a parameterized-query SQL Injection defense workbench';
        }

        if ($technology === 'csrf-protection') {
            return 'Built a CSRF token and SameSite protection workbench';
        }

        if ($technology === 'xss-defense') {
            return 'Built an XSS-safe Blade escaping and payload-test workbench';
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationSource($sourceItems)) {
            return 'Built a Broken Authentication lifecycle hardening artifact';
        }

        if ($technology === 'security-misconfiguration') {
            return 'Built a Security Misconfiguration production-readiness artifact';
        }

        if ($technology === 'idor-access-control') {
            return 'Built an IDOR object-level authorization review artifact';
        }

        if ($technology === 'oauth-flow') {
            return 'Built an OAuth Authorization Code with PKCE security workbench';
        }

        if ($technology === 'graph-traversal') {
            return 'Built a BFS versus DFS traversal decision artifact';
        }

        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesSourceItems($sourceItems)) {
            return 'Built a PHP stack versus heap runtime-memory artifact';
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesSourceItems($sourceItems)) {
            return 'Built an AI agent memory contract and governance artifact';
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesSourceItems($sourceItems)) {
            return 'Built a Predictive AI versus Generative AI comparison artifact';
        }

        if ($technology === 'rag-systems') {
            return 'Built a RAG, Long Context, and CAG chatbot strategy artifact';
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['source_items' => $sourceItems])) {
            return 'Built a database locking and lockForUpdate concurrency artifact';
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['source_items' => $sourceItems])) {
            return 'Built a PostgreSQL covering-index and heap-fetch evidence artifact';
        }

        return sprintf('Implemented content-backed %s practice in Laravel', $technology);
    }

    /**
     * Create a graph traversal README template for portfolio evidence.
     */
    private function graphTraversalReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# BFS Versus DFS Graph Traversal Practice Artifact

## Goal
Practice choosing BFS or DFS from traversal goal, graph shape, cycle risk, API crawling constraints, and database hierarchy constraints.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- BFS is explained through queue frontier, level-order traversal, nearest result, and shortest unweighted path behavior.
- DFS is explained through stack or recursion path, branch exploration, dependency reasoning, subtree validation, and backtracking behavior.
- Visited set, cycle detection, max depth, max nodes, fan-out, pagination, batching, rate limits, timeouts, and memory pressure are documented before promotion.
MARKDOWN;
    }

    /**
     * Create a RAG context-strategy README template for portfolio evidence.
     */
    private function ragContextStrategyReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# RAG, Long Context, and CAG Chatbot Strategy Artifact

## Goal
Practice choosing the right chatbot context strategy before implementation: retrieval-backed RAG, packed-document Long Context, cache-backed CAG, or hybrid routing.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Context strategy is selected from corpus freshness, size, stability, permissions, latency, cost, and evidence requirements.
- The answer contract records `selected_context_path`, `rag_pattern`, `token_budget`, `cache_version`, `source_version`, citations, fallback reason, and missing evidence.
- Guardrails cover tenant-scoped retrieval, packed-document token limits, CAG cache invalidation, source freshness, unauthorized chunks, and hybrid route selection.
MARKDOWN;
    }

    /**
     * Create an AI agent memory README template for portfolio evidence.
     */
    private function aiAgentMemoryReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# AI Agent Memory Practice Artifact

## Goal
Practice designing AI agent memory like an engineering contract: working memory, episodic memory, semantic memory, and procedural memory with governance evidence.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Working memory is bounded to current task state and expires before it becomes durable knowledge.
- Episodic memory records session or project history without crossing tenant, user, or privacy boundaries.
- Semantic memory stores durable facts with source, freshness, confidence, permission, retention, and correction metadata.
- Procedural memory points to reviewed playbooks and runbooks instead of letting the agent invent hidden workflow rules.
MARKDOWN;
    }

    /**
     * Create a covering-index README template for portfolio evidence.
     */
    private function coveringIndexReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# PostgreSQL Covering Index Practice Artifact

## Goal
Practice reducing heap fetches with a covering index, PostgreSQL INCLUDE columns, Index Only Scan verification, and operational guardrails.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Before and after `EXPLAIN (ANALYZE, BUFFERS)` output records Index Scan versus Index Only Scan and Heap Fetches.
- Key columns are chosen for filtering and ordering, while `INCLUDE` columns are limited to projected hot-query fields.
- Visibility map, VACUUM or autovacuum expectations, index size, bloat risk, write overhead, and rollback are documented before promotion.
MARKDOWN;
    }

    /**
     * Create a database-locking README template for portfolio evidence.
     */
    private function databaseLockingReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# Database Locking Practice Artifact

## Goal
Practice safe concurrent writes with protected invariants, `DB::transaction()`, `lockForUpdate()`, short row locks, deadlock handling, and lock-contention evidence.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- The protected invariant is named before the lock is added: stock cannot go negative, balance cannot be spent twice, or workflow state cannot move twice.
- `lockForUpdate()` runs inside `DB::transaction()` on a selective, indexed row lookup before the protected value is read and changed.
- External calls and slow work stay outside the transaction so the lock window remains short.
- Deadlock retry, lock timeout, hot-row contention, and lock-wait monitoring are reviewed before promotion.
MARKDOWN;
    }

    /**
     * Create an AI type comparison README template for portfolio evidence.
     */
    private function aiTypeComparisonReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# Predictive AI Versus Generative AI Practice Artifact

## Goal
Practice explaining the difference between Predictive AI and Generative AI through output contracts, input evidence, evaluation metrics, and failure modes.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Predictive AI is explained through scores, labels, forecasts, rankings, and risk decisions from existing data.
- Generative AI is explained through generated text, images, code, summaries, answers, or multimodal output from prompt and context.
- Quality evidence separates predictive metrics from generative checks and names failure modes for both sides.
MARKDOWN;
    }

    /**
     * Create a PHP runtime-memory README template for portfolio evidence.
     */
    private function phpRuntimeMemoryReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# PHP Stack Versus Heap Practice Artifact

## Goal
Practice PHP runtime-memory explanation with call frames, heap-backed arrays and objects, references, cleanup, and production memory risks.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Stack memory is explained through active function calls, parameters, local variables, return values, and frame cleanup.
- Heap-backed memory is explained through arrays, objects, strings, references, and object graphs.
- Cleanup evidence covers scope exit, unset(), reference counting, garbage collection, and long-running worker risk.
MARKDOWN;
    }

    /**
     * Create a React-specific README template for portfolio evidence.
     */
    private function reactRenderReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# React Render Optimization Practice Artifact

## Goal
Practice React render performance with evidence: measure first, then choose React.memo, useMemo, useCallback, state locality, or virtualization.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- React Profiler baseline is required before optimization.
- Memoization choice is tied to prop stability, derived-value cost, or callback identity.
- State/context/list rendering problems are considered before blanket memoization.
MARKDOWN;
    }

    /**
     * Create a JavaScript closure README template for portfolio evidence.
     */
    private function javascriptClosureReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# JavaScript Closure Practice Artifact

## Goal
Practice JavaScript closures with lexical scope, captured bindings, private state, interview loop traps, stale closures, and practical callback use.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Closure is explained as function plus access to variables from lexical scope.
- Captured binding behavior is proven with repeated createCounter() calls.
- Interview evidence covers private state, var versus let loop behavior, stale closures, debounce, throttle, memoization, and React hook dependencies.
MARKDOWN;
    }

    /**
     * Create an arrow-function this README template for portfolio evidence.
     */
    private function javascriptArrowThisReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# JavaScript Arrow Function This Practice Artifact

## Goal
Practice arrow-function `this` with lexical this, normal function call-site this, object-method traps, call/apply/bind limits, and callback use cases.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Arrow functions are explained as having no own `this`; they read `this` from the lexical scope where they are created.
- Normal functions are compared through call-site `this`, including object method calls and explicit binding.
- Interview evidence covers `obj.arrow()` traps, `call`/`apply`/`bind` limitations, timer callbacks, class callbacks, and when a normal method is safer.
MARKDOWN;
    }

    /**
     * Create a SQL Injection-specific README template for portfolio evidence.
     */
    private function sqlInjectionReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# SQL Injection Defense Practice Artifact

## Goal
Practice SQL Injection prevention with fixed SQL structure, parameterized values, allowlisted identifiers, and malicious payload tests.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- User-controlled values are bound through query builder, Eloquent, or raw SQL bindings.
- Dynamic identifiers are checked against allowlists before reaching SQL.
- Payload tests prove attacker input stays data instead of becoming query logic.
MARKDOWN;
    }

    /**
     * Create a CSRF-specific README template for portfolio evidence.
     */
    private function csrfProtectionReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# CSRF Protection Practice Artifact

## Goal
Practice CSRF prevention with browser intent, session-bound tokens, SameSite cookie review, safe HTTP methods, and missing-token tests.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- State-changing browser flows require token proof before mutation.
- SameSite cookie behavior is documented as a supporting control, not a token replacement.
- Feature tests prove missing or stale tokens do not change server state.
MARKDOWN;
    }

    /**
     * Create an XSS-specific README template for portfolio evidence.
     */
    private function xssDefenseReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# XSS Defense Practice Artifact

## Goal
Practice XSS prevention with context-aware escaping, safe JSON handoff, sanitized rich text, CSP limits, and browser payload tests.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Untrusted text is rendered through escaped output or another context-specific encoder.
- Raw HTML is removed, sanitized, or isolated behind a trusted-content boundary.
- Payload tests prove reflected, stored, and DOM-style input cannot execute script.
MARKDOWN;
    }

    /**
     * Create a Broken Authentication README template for portfolio evidence.
     */
    private function brokenAuthenticationReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# Broken Authentication Lifecycle Practice Artifact

## Goal
Practice Broken Authentication defense with login throttling, session lifecycle hardening, reset-token safety, logout invalidation, token revocation, MFA recovery review, and sensitive-log redaction.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Authentication is reviewed as a lifecycle: login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.
- Session fixation and reuse are handled through session regeneration, logout invalidation, and old-session rejection.
- Failure-path tests prove brute force, stale reset token, reused reset token, logged-out session reuse, and revoked token reuse fail closed.
- Security logs include suspicious-login context without passwords, reset tokens, session IDs, or bearer tokens.
MARKDOWN;
    }

    /**
     * Create a Security Misconfiguration README template for portfolio evidence.
     */
    private function securityMisconfigurationReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# Security Misconfiguration Practice Artifact

## Goal
Practice production configuration readiness with safe defaults, environment drift control, boundary hardening, and fail-closed deployment checks.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Production readiness checks cover `APP_DEBUG`, `APP_ENV`, config cache, verbose exception output, and log redaction.
- Secret exposure checks cover `.env` files, leaked credentials, key rotation ownership, public storage, and directory listing.
- Boundary hardening covers CORS allowlists, security headers, HTTPS enforcement, session cookie flags, and trusted proxies.
- Smoke checks fail release when configuration is missing, unsafe, or broader than documented.
MARKDOWN;
    }

    /**
     * Create an IDOR-specific README template for portfolio evidence.
     */
    private function idorAccessControlReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# IDOR Object-Level Authorization Practice Artifact

## Goal
Practice IDOR prevention with protected-object inventory, scoped lookup, object policies, nested-resource checks, denial tests, and monitoring evidence.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Every route that accepts an object identifier is reviewed for owner, tenant, role, and allowed action.
- Object lookup is scoped through the current user, tenant, organization, team, or parent resource before data is returned.
- Policy, Gate, FormRequest, or service authorization checks the exact object and requested action.
- Cross-user and cross-tenant tests prove ID swaps fail for read, update, delete, download, export, and nested-resource flows.
MARKDOWN;
    }

    /**
     * Create an OAuth-specific README template for portfolio evidence.
     */
    private function oauthFlowReadmeTemplate(array $commitPlan): string
    {
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# OAuth Authorization Code with PKCE Practice Artifact

## Goal
Practice OAuth flow selection with public-client PKCE proof, callback validation, token-boundary review, and failure-path tests.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Public clients use Authorization Code with PKCE instead of Implicit Flow.
- The authorize URL contains `code_challenge` and `code_challenge_method=S256`, but never `code_verifier`.
- Callback validation covers state mismatch, redirect URI mismatch, reused code, and token fields in the URL.
- Token handling documents audience, scope, lifetime, refresh rotation, storage boundary, and log redaction.
MARKDOWN;
    }

    /**
     * Detect arrow-function `this` records inside the broader JavaScript closure lane.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     */
    private function isArrowThisSource(array $sourceItems): bool
    {
        $haystack = Str::lower(collect($sourceItems)
            ->flatMap(fn (array $item): array => [
                $item['content']['title'] ?? '',
                $item['content']['body'] ?? '',
                $item['task'] ?? '',
            ])
            ->implode(' '));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, ' this') || str_contains($haystack, '`this`') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect broken-authentication records inside the broader auth-security lane.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     */
    private function isBrokenAuthenticationSource(array $sourceItems): bool
    {
        $haystack = Str::lower(collect($sourceItems)
            ->flatMap(fn (array $item): array => [
                $item['content']['title'] ?? '',
                $item['content']['body'] ?? '',
                $item['task'] ?? '',
            ])
            ->implode(' '));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }
}
