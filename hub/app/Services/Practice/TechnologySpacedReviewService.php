<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologySpacedReviewService
{
    /**
     * Create spaced review schedules from mastery checkpoints.
     */
    public function __construct(
        private readonly TechnologyMasteryCheckpointService $checkpoints,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build day 1, day 3, and day 7 review cards for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $checkpoint = $this->checkpoints->build($technology, $filters);
        $cards = $this->cardsFor($technology, $checkpoint);

        return [
            'title' => sprintf('Technology Spaced Review: %s', $technology),
            'technology' => $technology,
            'checkpoint' => $checkpoint,
            'cards' => $cards,
            'promotion_criteria' => [
                'Day 1 recall names the source record, changed files, and verification command without hints.',
                'Day 3 rebuild can complete one source-backed task with fewer generated prompts.',
                'Day 7 defense can explain the tradeoff and cite evidence in under two minutes.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $cards,
                fn (array $card): string => $card['label'],
            ),
        ];
    }

    /**
     * Build spaced review cards from checkpoint proof and handoff data.
     *
     * @return array<int, array{day: int, label: string, recall_prompt: string, evidence_recheck: string, coding_action: string}>
     */
    private function cardsFor(string $technology, array $checkpoint): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesRemediation($checkpoint['remediation_plan'] ?? [])) {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain stack memory as active PHP call frames, then name parameters, local variables, return values, and frame cleanup.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm call-frame model proof exists.',
                    'coding_action' => 'Open the implementation lab and identify the function call, local frame state, heap-backed data, and cleanup note without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the example before opening snippets: function frame first, large array or object graph second, cleanup note third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm heap-backed data proof still exists.',
                    'coding_action' => 'Write one tiny PHP example with a large array or object reference and add a memory-risk note for long-running workers.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using call frames, heap-backed data, references, GC, cleanup, and PHP caveats.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the memory model is clear enough to explain without manual-allocation claims.',
                ],
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain SQL Injection as input becoming SQL logic, then name the exact binding or query builder defense you used.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm injection-mechanism proof exists.',
                    'coding_action' => 'Open the workbench and identify the unsafe query, safe binding, allowlist rule, and test payload without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the safe query path before opening snippets: value binding first, identifier allowlist second.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm parameterized-value proof still exists.',
                    'coding_action' => 'Write one new failing payload test for OR 1=1 or invalid sort input, then make it pass.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using mechanism, parameterized query, identifier allowlist, payload tests, and changed-file evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether this query-hardening pattern can be reused in another endpoint.',
                ],
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain CSRF as forged browser intent, then name why cookies being sent automatically matters.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm CSRF attack-mechanism proof exists.',
                    'coding_action' => 'Open the workbench and identify the token proof, SameSite setting, method boundary, and failure test without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the safe state-changing flow before opening snippets: non-GET method, token proof, SameSite review, failure test.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm CSRF token-proof evidence still exists.',
                    'coding_action' => 'Write one new failing test for missing token or stale token behavior, then make it pass.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using browser cookies, request intent, CSRF token, SameSite, failure tests, and changed-file evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether this CSRF pattern applies to a Blade form, Sanctum SPA, or bearer-token API.',
                ],
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain reflected, stored, and DOM-based XSS as untrusted data reaching executable browser context.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm XSS variant proof exists.',
                    'coding_action' => 'Open the workbench and identify escaped Blade output, safe JSON handoff, raw HTML boundary, and payload test without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the safe rendering path before opening snippets: escaped text first, sanitized rich text second, CSP as backup.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm context-aware escaping proof still exists.',
                    'coding_action' => 'Write one new failing payload test for a script tag, event handler, or unsafe DOM sink, then make it pass.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using XSS variant, output context, safe rendering boundary, payload tests, CSP limits, and changed-file evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether this safe-rendering pattern applies to Blade, API JSON, or client-side DOM updates.',
                ],
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain Security Misconfiguration as unsafe runtime or deployment setup, then name one dangerous production default.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm unsafe-default detection proof exists.',
                    'coding_action' => 'Open the implementation lab and identify APP_DEBUG, secret exposure, storage visibility, CORS, headers, cookie flags, and trusted proxy checks without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the readiness check before opening snippets: unsafe default first, environment owner second, fail-closed smoke check third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][2] ?? 'Confirm boundary-hardening proof still exists.',
                    'coding_action' => 'Write one new failing configuration smoke check for debug mode, exposed secrets, broad CORS, missing headers, or proxy drift, then make it pass.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using unsafe defaults, environment drift, CORS, headers, cookies, proxies, smoke checks, owners, and rollback evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the readiness pattern applies to a second environment or deployment profile.',
                ],
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain IDOR as broken object-level authorization, then name why a logged-in user can still be unauthorized for a specific object.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm object-surface inventory proof exists.',
                    'coding_action' => 'Open the workbench and identify the object route, route parameter, owner or tenant boundary, scoped lookup, and policy check without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the IDOR fix before opening snippets: route inventory first, scoped lookup second, object policy third, denial test fourth.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm scoped-lookup proof still exists.',
                    'coding_action' => 'Write one new failing test where user A replays user B object ID for read, update, download, export, or nested-resource access, then make it pass.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using authentication versus authorization, scoped lookup, object policy, ID-swap tests, 403 or 404 behavior, and monitoring evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the IDOR pattern applies to another object route, nested resource, download, or export path.',
                ],
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain PKCE as public-client proof, then name why code_verifier must stay private while code_challenge is sent.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm OAuth flow-fit proof exists.',
                    'coding_action' => 'Open the workbench and identify client type, authorize URL, S256 challenge, verifier storage, and callback checks without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the login proof before opening snippets: generate verifier, derive S256 challenge, validate state, exchange code with verifier.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm PKCE proof still exists.',
                    'coding_action' => 'Write one new failing test for missing verifier, wrong verifier, reused code, or token fields in callback input, then make it pass.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using client type, Authorization Code with PKCE, S256 challenge, callback validation, token boundary, and changed-file evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether this OAuth pattern applies to a browser SPA, mobile app, or confidential server app.',
                ],
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain BFS versus DFS from traversal goal first, then name queue frontier, stack or recursion path, and shortest unweighted path behavior.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm traversal-goal proof exists.',
                    'coding_action' => 'Open the implementation lab and identify the BFS queue example, DFS stack or recursion example, visited set, and traversal order without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the traversal example before opening snippets: graph fixture first, BFS order second, DFS order third, cycle guard fourth.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][2] ?? 'Confirm cycle-safety proof still exists.',
                    'coding_action' => 'Write one new cyclic graph fixture and prove max depth, max nodes, or visited-set handling prevents repeated work.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using traversal goal, BFS queue, DFS stack or recursion, visited set, shortest path, API crawling, database hierarchy, and memory evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether BFS or DFS fits the new API or database hierarchy case before reading snippets.',
                ],
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisCheckpoint($checkpoint)) {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain arrow-function `this` as lexical this, then name the surrounding scope that supplies `this`.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm lexical-this proof exists.',
                    'coding_action' => 'Open the implementation lab and compare one normal object method with one arrow property without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the arrow-this example before opening snippets: normal method first, arrow property second, callback use third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm call-site comparison proof still exists.',
                    'coding_action' => 'Write one new obj.arrow() trap note and one call/apply/bind limitation note.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using lexical this, normal function call-site this, obj.arrow() trap, call/apply/bind limits, and callback use.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether each function should be an arrow callback or a normal object method.',
                ],
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain a JavaScript closure as function plus lexical scope, then identify the captured binding in createCounter().',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm lexical-scope proof exists.',
                    'coding_action' => 'Open the implementation lab and trace the outer scope, inner function, captured variable, and repeated-call output without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the closure example before opening snippets: outer variable first, returned inner function second, repeated calls third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm captured-binding proof still exists.',
                    'coding_action' => 'Write one new var versus let loop callback example and one stale-closure note for async code, timers, or hooks.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using lexical scope, captured binding, private state, var versus let, stale closures, and practical callback use.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the closure explanation is clear enough to defend without syntax-only wording.',
                ],
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesRemediation($checkpoint['remediation_plan'] ?? [])) {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain AI agent memory as four separate contracts: working, episodic, semantic, and procedural memory.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm memory-contract separation proof exists.',
                    'coding_action' => 'Open the implementation lab and identify the current-task state, session-history record, durable fact, and reviewed playbook without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the memory design before opening snippets: memory type first, lifetime second, trust metadata third, failure path fourth.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm metadata and governance proof still exists.',
                    'coding_action' => 'Write one new memory contract row with source, freshness, confidence, permission scope, retention rule, and correction path.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using working memory limits, episodic privacy boundaries, semantic freshness, procedural review, stale fallback, and private-memory blocking.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the agent should remember, retrieve, ask again, or discard each piece of context.',
                ],
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesRemediation($checkpoint['remediation_plan'] ?? [])) {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain Predictive AI as scores, labels, forecasts, rankings, or risk decisions, then contrast it with Generative AI output.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm AI output-contract proof exists.',
                    'coding_action' => 'Open the implementation lab and identify one predictive output contract and one generative output contract without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the comparison before opening snippets: output contract first, input evidence second, evaluation metric third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][2] ?? 'Confirm evaluation-fit proof still exists.',
                    'coding_action' => 'Write one new Predictive AI example and one Generative AI example with separate metrics and failure controls.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using product fit, output contract, input evidence, predictive metrics, generative checks, and failure-mode evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the comparison is clear enough to defend without generic AI claims.',
                ],
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($checkpoint['remediation_plan'] ?? [])) {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain database locking from the protected invariant first, then name why concurrent requests can oversell inventory or double-spend balance.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm protected-invariant proof exists.',
                    'coding_action' => 'Open the implementation lab and identify the transaction boundary, locked row lookup, protected read, validation, and write without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the locking fix before opening snippets: `DB::transaction()` first, `lockForUpdate()` second, protected state check third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm transaction-boundary proof still exists.',
                    'coding_action' => 'Write one new locking example and remove any external call, queue dispatch, sleep, or slow computation from inside the transaction.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using invariant, row lock, transaction boundary, indexed lookup, deadlock retry, timeout, contention, and monitoring evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether another write path needs a row lock or a different concurrency pattern.',
                ],
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesRemediation($checkpoint['remediation_plan'] ?? [])) {
            return [
                [
                    'day' => 1,
                    'label' => 'Day 1 recall',
                    'recall_prompt' => 'Explain why Index Scan can still be slow when Heap Fetches force random IO back to the table heap.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm query-plan baseline proof exists.',
                    'coding_action' => 'Open the implementation lab and identify the selected columns, filter columns, order columns, Heap Fetches count, and buffer-read evidence without editing.',
                ],
                [
                    'day' => 3,
                    'label' => 'Day 3 rebuild',
                    'recall_prompt' => 'Rebuild the covering-index proposal before opening snippets: key columns first, INCLUDE columns second, visibility-map note third.',
                    'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm INCLUDE design proof still exists.',
                    'coding_action' => 'Write one new CREATE INDEX proposal with INCLUDE, then remove any included column that is not projected by the hot query.',
                ],
                [
                    'day' => 7,
                    'label' => 'Day 7 defense',
                    'recall_prompt' => 'Answer the interview question using EXPLAIN, Heap Fetches, Index Only Scan, INCLUDE, visibility map, VACUUM, bloat, write overhead, and rollback evidence.',
                    'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                    'coding_action' => 'Open the next challenge and decide whether the covering-index pattern applies to another hot query or would create too much write overhead.',
                ],
            ];
        }

        return [
            [
                'day' => 1,
                'label' => 'Day 1 recall',
                'recall_prompt' => sprintf('Explain what `%s` implementation you built and which JSON source record started it.', $technology),
                'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm first proof item exists.',
                'coding_action' => 'Open the implementation lab and identify the route, controller, service, and test without editing.',
            ],
            [
                'day' => 3,
                'label' => 'Day 3 rebuild',
                'recall_prompt' => 'Describe the Laravel layer placement before opening the generated snippets.',
                'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm repair proof still exists.',
                'coding_action' => sprintf('Rebuild one `%s` task from the implementation lab with only the source title visible.', $technology),
            ],
            [
                'day' => 7,
                'label' => 'Day 7 defense',
                'recall_prompt' => 'Answer the interview pack questions using source, changed-file, and verification evidence.',
                'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                'coding_action' => 'Open the next challenge and decide whether to promote or repeat.',
            ],
        ];
    }

    /**
     * Detect arrow-function `this` work inside the broader JavaScript closure lane.
     */
    private function isArrowThisCheckpoint(array $checkpoint): bool
    {
        $haystack = strtolower((string) json_encode($checkpoint, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }
}
