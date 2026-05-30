<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyInterviewPackService
{
    /**
     * Create interview defense packs from technology portfolio artifacts.
     */
    public function __construct(
        private readonly TechnologyPortfolioArtifactService $artifacts,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build interview questions, answer outlines, and evidence for one technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $artifact = $this->artifacts->build($technology, $filters);

        return [
            'title' => sprintf('Technology Interview Pack: %s', $technology),
            'technology' => $technology,
            'artifact' => $artifact,
            'questions' => $this->questionsFor($technology, $artifact),
            'evidence_to_cite' => $this->evidenceToCite($artifact),
            'practice_script' => $this->practiceScript($technology, $artifact),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Answer architecture question',
                'Answer implementation question',
                'Answer testing question',
                'Cite changed files and verification',
                'Practice concise oral summary',
            ]),
        ];
    }

    /**
     * Build interview questions with answer outlines.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function questionsFor(string $technology, array $artifact): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesArtifact($artifact)) {
            return $this->phpRuntimeMemoryQuestions($artifact);
        }

        if ($technology === 'react-render-performance') {
            return $this->reactRenderQuestions($artifact);
        }

        if ($technology === 'javascript-closures') {
            if ($this->isArrowThisArtifact($artifact)) {
                return $this->javascriptArrowThisQuestions($artifact);
            }

            return $this->javascriptClosureQuestions($artifact);
        }

        if ($technology === 'sql-injection-defense') {
            return $this->sqlInjectionQuestions($artifact);
        }

        if ($technology === 'csrf-protection') {
            return $this->csrfProtectionQuestions($artifact);
        }

        if ($technology === 'xss-defense') {
            return $this->xssDefenseQuestions($artifact);
        }

        if ($technology === 'idor-access-control') {
            return $this->idorAccessControlQuestions($artifact);
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationArtifact($artifact)) {
            return $this->brokenAuthenticationQuestions($artifact);
        }

        if ($technology === 'security-misconfiguration') {
            return $this->securityMisconfigurationQuestions($artifact);
        }

        if ($technology === 'oauth-flow') {
            return $this->oauthFlowQuestions($artifact);
        }

        if ($technology === 'graph-traversal') {
            return $this->graphTraversalQuestions($artifact);
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesArtifact($artifact)) {
            return $this->aiAgentMemoryQuestions($artifact);
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesArtifact($artifact)) {
            return $this->aiTypeComparisonQuestions($artifact);
        }

        if ($technology === 'rag-systems') {
            return $this->ragContextStrategyQuestions($artifact);
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($artifact)) {
            return $this->databaseLockingQuestions($artifact);
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact($artifact)) {
            return $this->coveringIndexQuestions($artifact);
        }

        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected source record');

        return [
            [
                'question' => sprintf('How did you turn `%s` learning content into Laravel code?', $technology),
                'answer_outline' => [
                    sprintf('I started from the JSON source record `%s`.', $sampleTitle),
                    'I mapped the content to a technology-specific practice task.',
                    'I implemented code in Laravel files instead of changing the source JSON.',
                    'I verified the behavior with focused commands.',
                ],
            ],
            [
                'question' => 'Which Laravel layers did you touch and why?',
                'answer_outline' => [
                    'Routes expose the URL or API entry point only.',
                    'Controllers orchestrate requests and delegate behavior.',
                    'Services contain the implementation logic for the practice task.',
                    'Tests and verification commands prove the flow works.',
                ],
            ],
            [
                'question' => 'How would you review this work before merging it?',
                'answer_outline' => [
                    'Check that changed files match the technology and source records.',
                    'Confirm validation, authorization, storage, queue, cache, or view responsibilities are in the right layer.',
                    'Run the listed verification commands and capture evidence.',
                    'Use the review checklist from the commit plan.',
                ],
            ],
        ];
    }

    /**
     * Build concrete evidence references the learner can cite.
     *
     * @return array<int, string>
     */
    private function evidenceToCite(array $artifact): array
    {
        return [
            sprintf('Branch: %s', $artifact['commit_plan']['branch']),
            sprintf('Commit message: %s', $artifact['commit_plan']['commit_message']),
            sprintf('Changed files: %s', implode(', ', $artifact['portfolio']['changed_files'])),
            sprintf('Verification: %s', implode(' | ', $artifact['portfolio']['verification'])),
            sprintf('Source records covered: %d', count($artifact['portfolio']['source_coverage'])),
        ];
    }

    /**
     * Build a short oral practice script.
     *
     * @return array<int, string>
     */
    private function practiceScript(string $technology, array $artifact): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesArtifact($artifact)) {
            return [
                'Stack memory is the mental model for active function calls: parameters, local variables, return values, and frame cleanup.',
                'Heap-backed data covers arrays, objects, strings, references, and object graphs whose lifetime can outlive one call frame.',
                sprintf('The related implementation evidence is traceable through `%s` and should name recursion, large arrays, or long-running worker memory risk.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'react-render-performance') {
            return [
                'I would not start by wrapping everything in memo. I would capture a React Profiler baseline first.',
                'If the cause is prop churn, I stabilize object, array, or callback identity and memoize only expensive children.',
                sprintf('The related implementation evidence is traceable through `%s` and the listed verification commands.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'javascript-closures') {
            if ($this->isArrowThisArtifact($artifact)) {
                return [
                    'Arrow functions do not create their own `this`; they read `this` from the lexical scope where they are created.',
                    'Normal functions decide `this` from the call site, such as object method calls, constructors, or call/apply/bind.',
                    sprintf('The related implementation evidence is traceable through `%s` and should cover object-method traps, callbacks, timers, and call/apply/bind behavior.', $artifact['commit_plan']['branch']),
                ];
            }

            return [
                'A JavaScript closure is a function that keeps access to variables from the lexical scope where it was created.',
                'The important point is captured variable binding, not a one-time copy of the value.',
                sprintf('The related implementation evidence is traceable through `%s` and should cover counters, private state, var versus let, and stale closures.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'sql-injection-defense') {
            return [
                'SQL Injection is when user input becomes SQL logic instead of staying data.',
                'My first fix is parameterized queries or query builder bindings for values, then allowlists for identifiers such as sort columns.',
                sprintf('The related implementation evidence is traceable through `%s` and includes malicious payload tests.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'csrf-protection') {
            return [
                'CSRF is when a logged-in browser is tricked into sending an unwanted state-changing request.',
                'My first controls are Laravel CSRF tokens, non-GET state-changing methods, and deliberate SameSite cookie settings.',
                sprintf('The related implementation evidence is traceable through `%s` and includes missing-token or stale-token tests.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'xss-defense') {
            return [
                'XSS is when untrusted data reaches an executable browser context.',
                'My first controls are context-aware escaping, safe JSON handoff, sanitizer review for rich text, and CSP as defense-in-depth.',
                sprintf('The related implementation evidence is traceable through `%s` and includes reflected, stored, or DOM-style payload tests.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                'IDOR is object-level authorization failure: a logged-in user can change an object ID and reach data they should not access.',
                'My first controls are scoped queries before model resolution, policy or Gate checks on the exact object, and denial tests with a second user or tenant.',
                sprintf('The related implementation evidence is traceable through `%s` and includes ID-swap replay notes, 403 or 404 reasoning, and object-level denial tests.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationArtifact($artifact)) {
            return [
                'Broken authentication is any weakness that lets the wrong person become, stay, or act as a user.',
                'I review the full lifecycle: login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                sprintf('The related implementation evidence is traceable through `%s` and should include throttling, session regeneration, reset-token expiry, revocation, and suspicious-login logging.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                'Security Misconfiguration is unsafe runtime, deployment, or infrastructure setup that exposes data or weakens app defenses.',
                'My first checks are debug mode, secret exposure, public storage, CORS, security headers, cookie flags, trusted proxies, and environment drift.',
                sprintf('The related implementation evidence is traceable through `%s` and should include configuration readiness smoke checks.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                'PKCE protects public-client authorization code flow by proving the token exchange came from the client that started login.',
                'My first checks are client type, S256 code_challenge, private code_verifier handling, state validation, and callback token leakage.',
                sprintf('The related implementation evidence is traceable through `%s` and includes verifier failure tests.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                'BFS and DFS are traversal strategies chosen from the goal, not from a universal speed rule.',
                'BFS uses a queue and fits level-order search, nearest result, and shortest path in an unweighted graph; DFS uses a stack or recursion and fits branch exploration, dependency reasoning, subtree validation, and backtracking.',
                sprintf('The related implementation evidence is traceable through `%s` and should include traversal order, visited set, cycle, depth, fan-out, pagination, rate-limit, and memory guardrails.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesArtifact($artifact)) {
            return [
                'AI agent memory should be split into working, episodic, semantic, and procedural memory instead of treated as one prompt-history bucket.',
                'Each memory type needs a different lifetime, trust level, permission boundary, correction path, and failure test.',
                sprintf('The related implementation evidence is traceable through `%s` and should include freshness, confidence, source, retention, private-memory blocking, and stale-memory fallback checks.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesArtifact($artifact)) {
            return [
                'Predictive AI estimates outcomes such as scores, labels, forecasts, rankings, or risk decisions from existing data.',
                'Generative AI creates new text, image, code, summary, answer, or multimodal output from prompt, context, and learned patterns.',
                sprintf('The related implementation evidence is traceable through `%s` and separates prediction metrics from generation quality checks.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                'RAG, Long Context, and CAG are context strategies for grounding a chatbot, not competing model names.',
                'I choose RAG for broad or changing corpora, Long Context for bounded document packs, CAG for stable cached knowledge, and hybrid routing when the chatbot needs more than one path.',
                sprintf('The related implementation evidence is traceable through `%s` and should include context routing, citations, permissions, token budget, cache version, source freshness, and fallback tests.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($artifact)) {
            return [
                'Database locking protects a critical row or resource from unsafe concurrent writes, but it only works when the transaction boundary is correct.',
                '`lockForUpdate()` should run inside `DB::transaction()` before checking and mutating the protected value, with slow external work kept outside the lock.',
                sprintf('The related implementation evidence is traceable through `%s` and should include concurrent-request, deadlock, timeout, and contention notes.', $artifact['commit_plan']['branch']),
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact($artifact)) {
            return [
                'A covering index helps a hot query read all needed values from the index instead of fetching rows from the heap.',
                'In PostgreSQL, INCLUDE columns can support Index Only Scan, but visibility map health and VACUUM determine whether heap fetches disappear in practice.',
                sprintf('The related implementation evidence is traceable through `%s` and should include before and after EXPLAIN output.', $artifact['commit_plan']['branch']),
            ];
        }

        return [
            sprintf('I practiced `%s` by mapping JSON learning records into Laravel implementation tasks.', $technology),
            sprintf('The work is traceable through `%s` and the changed files listed in the artifact.', $artifact['commit_plan']['branch']),
            'I can explain the source record, the Laravel layer placement, the tests, and the verification evidence.',
        ];
    }

    /**
     * Build RAG context-strategy interview prompts around routing, contracts, and guardrails.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function ragContextStrategyQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected RAG context strategy topic');

        return [
            [
                'question' => 'How do you choose between RAG, Long Context, CAG, and hybrid routing for a chatbot?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and classify the context need before picking a retrieval pattern.', $sampleTitle),
                    'RAG fits broad, frequently changing, or permission-sensitive corpora where retrieval freshness matters.',
                    'Long Context fits bounded document packs when the model can receive the whole relevant packet within token and cost limits.',
                    'CAG fits stable FAQ, policy, or product knowledge where cached context can be versioned and invalidated safely.',
                ],
            ],
            [
                'question' => 'What should a production chatbot answer contract expose?',
                'answer_outline' => [
                    'Return selected_context_path, rag_pattern, token_budget, cache_version, source_version, fallback_reason, and missing_evidence.',
                    'Attach citations or source IDs so the answer can be audited after the request finishes.',
                    'Scope retrieval filters, cache keys, packed documents, and citations by user, tenant, permission, locale, and content version.',
                    'Do not return a confident answer when evidence is stale, unauthorized, missing, or below threshold.',
                ],
            ],
            [
                'question' => 'How would you test the chatbot context router?',
                'answer_outline' => [
                    'Test RAG retrieval permission filters and source freshness.',
                    'Test Long Context token budget, truncation behavior, and source markers.',
                    'Test CAG cache versioning, invalidation, tenant-specific cache keys, and stale-cache fallback.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build AI agent memory interview prompts around memory contracts and governance.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function aiAgentMemoryQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected AI agent memory topic');

        return [
            [
                'question' => 'What are the four core memory types an AI agent needs to work like a developer?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and define memory as separate contracts, not one chat log.', $sampleTitle),
                    'Working memory stores current task state and should be bounded, short-lived, and easy to discard.',
                    'Episodic memory stores project or session history and must respect user, tenant, and privacy boundaries.',
                    'Semantic memory stores durable facts, while procedural memory points to reviewed playbooks and repeatable workflows.',
                ],
            ],
            [
                'question' => 'What metadata should an AI agent memory record carry before it is trusted?',
                'answer_outline' => [
                    'Record source, freshness, confidence, permission scope, retention rule, and correction path.',
                    'Separate private session notes from facts that can be reused across projects.',
                    'Require stale-memory fallback when source age, confidence, or permission is not acceptable.',
                    'Do not let procedural memory become hidden instructions that bypass code review or security policy.',
                ],
            ],
            [
                'question' => 'How would you test an AI agent memory design before shipping it?',
                'answer_outline' => [
                    'Test working memory expiry so temporary task state does not become durable knowledge.',
                    'Test cross-user, cross-tenant, and private-memory blocking for episodic memory.',
                    'Test stale semantic facts, corrected facts, low-confidence facts, and unreviewed procedural playbooks.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build graph traversal interview prompts around BFS, DFS, and production guardrails.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function graphTraversalQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected graph traversal topic');

        return [
            [
                'question' => 'How do you choose between BFS and DFS in a real system?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and identify the traversal goal.', $sampleTitle),
                    'BFS fits nearest result, level-order traversal, and shortest path in an unweighted graph.',
                    'DFS fits branch exploration, dependency reasoning, nested subtree validation, and backtracking.',
                    'The answer should name the data shape, stopping condition, memory budget, and failure modes.',
                ],
            ],
            [
                'question' => 'How do BFS and DFS differ in state, order, and memory cost?',
                'answer_outline' => [
                    'BFS uses a queue and keeps a frontier of nodes at the current or next distance.',
                    'DFS uses a stack or recursion and follows one branch before siblings.',
                    'BFS can use more memory on wide graphs; DFS can run into recursion depth or miss nearest results if used for the wrong goal.',
                    'Both need visited-set handling when the structure can contain cycles.',
                ],
            ],
            [
                'question' => 'How would you apply BFS or DFS to APIs and database hierarchies?',
                'answer_outline' => [
                    'For API crawling, BFS can cap hop count, rate-limit per level, and stop at the closest matching resource.',
                    'For database category trees, level-order batching fits expandable UI, while DFS fits full subtree validation or nested menu rendering.',
                    'Production code needs max depth, max nodes, fan-out limits, pagination, batching, timeout, and memory guardrails.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build React-specific interview prompts around measurement and memoization tradeoffs.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function reactRenderQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected React render topic');

        return [
            [
                'question' => 'How do you decide whether React.memo, useMemo, or useCallback is the right fix?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and a React Profiler baseline.', $sampleTitle),
                    'React.memo is for expensive children whose props are stable.',
                    'useMemo is for expensive derived values or stable object/array props.',
                    'useCallback is for callback identity passed to memoized children or dependency-sensitive hooks.',
                ],
            ],
            [
                'question' => 'When does memoization not solve a React re-render problem?',
                'answer_outline' => [
                    'If props change every render, React.memo cannot skip the child.',
                    'If state or context updates are too broad, move state closer or split context first.',
                    'If a list renders too many DOM nodes, virtualization or pagination usually matters more.',
                    'If the component is cheap, memoization can make the code harder to maintain without visible benefit.',
                ],
            ],
            [
                'question' => 'What evidence would you show before merging a React render optimization?',
                'answer_outline' => [
                    'Before and after React Profiler commit duration.',
                    'Rendered component count or prop-change evidence.',
                    'Dependency review for useMemo and useCallback to avoid stale closures.',
                    sprintf('The changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build JavaScript closure interview prompts around lexical scope and captured bindings.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function javascriptClosureQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected JavaScript closure topic');

        return [
            [
                'question' => 'What is a JavaScript closure and why does lexical scope matter?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and define closure as function plus lexical environment.', $sampleTitle),
                    'A closure lets an inner function access variables from the scope where it was created.',
                    'Those variables stay reachable while the returned or stored function can still run.',
                    'The function keeps access to the variable binding, not just a one-time copied value.',
                ],
            ],
            [
                'question' => 'Which practical JavaScript patterns depend on closures?',
                'answer_outline' => [
                    'Function factories such as createCounter() use closure state between calls.',
                    'Private state, event handlers, callbacks, debounce, throttle, and memoization all rely on captured scope.',
                    'React hook callbacks can also expose stale-closure bugs when dependencies are wrong.',
                    'Closures are useful, but retained references can keep data alive longer than expected.',
                ],
            ],
            [
                'question' => 'What interview traps show whether someone really understands closure?',
                'answer_outline' => [
                    'Explain why var in a loop can share one binding while let creates a new block-scoped binding per iteration.',
                    'Trace counter output across repeated function calls instead of describing the syntax only.',
                    'Identify stale closures in async callbacks, timers, or hooks.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build arrow-function `this` interview prompts around lexical this and call-site traps.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function javascriptArrowThisQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected arrow-function this topic');

        return [
            [
                'question' => 'Why does an arrow function not have its own `this`?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and define arrow `this` as lexical `this`.', $sampleTitle),
                    'The arrow function reads `this` from the surrounding scope where the arrow is created.',
                    'Calling the arrow later does not create a fresh `this` binding.',
                    'This is why arrow callbacks can keep class or object context from the outer function.',
                ],
            ],
            [
                'question' => 'How is `this` different in a normal function declaration or method?',
                'answer_outline' => [
                    'A normal function receives `this` from the call site.',
                    'obj.method() usually makes `this` point to obj, while a plain function call may be undefined in strict mode.',
                    'call(), apply(), and bind() can set `this` for normal functions.',
                    'Those APIs cannot rebind `this` for an arrow function because it was already taken lexically.',
                ],
            ],
            [
                'question' => 'What interview traps should you mention for arrow function `this`?',
                'answer_outline' => [
                    'Do not use an arrow as an object method when the method needs `this` to mean that object.',
                    'obj.arrow() does not make `this` become obj; the arrow already closed over outer `this`.',
                    'Arrow callbacks are useful inside timers, event handlers, array callbacks, and class code when you intentionally want the outer `this`.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build PHP runtime-memory interview prompts around stack frames and heap data.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function phpRuntimeMemoryQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected PHP runtime-memory topic');

        return [
            [
                'question' => 'How do stack memory and heap memory differ in a PHP runtime explanation?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and frame the answer as a mental model, not manual allocation.', $sampleTitle),
                    'The stack tracks active function calls, parameters, local variables, return values, and frame cleanup.',
                    'The heap holds larger or dynamically-lifetime data such as arrays, objects, strings, references, and object graphs.',
                    'In normal PHP application code, developers influence memory indirectly through data shape, references, scope, and cleanup.',
                ],
            ],
            [
                'question' => 'What PHP example would you use to make stack versus heap visible?',
                'answer_outline' => [
                    'Use a small function call to show call-frame state and return/unwind behavior.',
                    'Create a large array or object graph to show heap pressure and reference lifetime.',
                    'Use unset(), scope exit, or memory_get_usage() as observation aids without pretending PHP exposes raw heap allocation.',
                    'Explain what changes in a short request lifecycle versus a long-running worker.',
                ],
            ],
            [
                'question' => 'What mistakes do you watch for in stack and heap interview answers?',
                'answer_outline' => [
                    'Do not say PHP developers manually choose stack or heap allocation in normal application code.',
                    'Do not confuse reference variables with raw pointers or manual memory management.',
                    'Name real failure modes: deep recursion, large arrays, retained references, circular references, or stale worker state.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Detect whether the JavaScript closure artifact is about arrow-function `this`.
     */
    private function isArrowThisArtifact(array $artifact): bool
    {
        $haystack = strtolower(implode(' ', [
            $artifact['meta']['filters']['search'] ?? '',
            $artifact['commit_plan']['branch'] ?? '',
            ...collect($artifact['portfolio']['source_coverage'] ?? [])
                ->flatMap(fn (array $item): array => [
                    $item['title'] ?? '',
                    $item['task'] ?? '',
                    $item['source_path'] ?? '',
                ])
                ->all(),
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect whether an auth-security artifact focuses on broken authentication.
     */
    private function isBrokenAuthenticationArtifact(array $artifact): bool
    {
        $haystack = strtolower(implode(' ', [
            $artifact['meta']['filters']['search'] ?? '',
            $artifact['commit_plan']['branch'] ?? '',
            ...collect($artifact['portfolio']['source_coverage'] ?? [])
                ->flatMap(fn (array $item): array => [
                    $item['title'] ?? '',
                    $item['task'] ?? '',
                    $item['source_path'] ?? '',
                ])
                ->all(),
        ]));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }

    /**
     * Build SQL Injection-specific interview prompts around bindings and boundary review.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function sqlInjectionQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected SQL Injection topic');

        return [
            [
                'question' => 'What is SQL Injection and how do parameterized queries prevent it?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and explain the input boundary.', $sampleTitle),
                    'SQL Injection happens when user input changes SQL structure or logic.',
                    'Parameterized queries keep SQL structure fixed and send user values separately as bindings.',
                    'Escaping is not the primary defense because it is easier to miss context-specific cases.',
                ],
            ],
            [
                'question' => 'What do you review before applying raw SQL or dynamic filters?',
                'answer_outline' => [
                    'Every user-controlled value must be bound, not concatenated.',
                    'Dynamic identifiers such as table names, column names, and sort directions need allowlists.',
                    'Query builder and Eloquent are preferred for normal value filters.',
                    'Raw SQL must show positional or named bindings in the same review.',
                ],
            ],
            [
                'question' => 'How would you prove the fix works?',
                'answer_outline' => [
                    'Test classic payloads such as OR 1=1 and quoted comment probes.',
                    'Test unsafe sort or column inputs separately because identifiers are not value-bound.',
                    'Confirm rejected input returns validation errors or harmless literal search results.',
                    sprintf('The changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build CSRF-specific interview prompts around browser intent and cookie controls.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function csrfProtectionQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected CSRF topic');

        return [
            [
                'question' => 'What is CSRF and why does it matter for cookie-based auth?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and explain the browser boundary.', $sampleTitle),
                    'CSRF tricks a logged-in browser into sending an unwanted state-changing request.',
                    'It matters when cookies carry identity because browsers attach eligible cookies automatically.',
                    'A CSRF token proves the request came through the intended application flow.',
                ],
            ],
            [
                'question' => 'How do CSRF tokens and SameSite cookies differ?',
                'answer_outline' => [
                    'CSRF tokens prove request intent and session linkage.',
                    'SameSite changes when cookies are sent in cross-site contexts.',
                    'SameSite reduces exposure but does not replace token validation for sensitive state changes.',
                    'GET should stay read-only so simple cross-site navigation cannot mutate state.',
                ],
            ],
            [
                'question' => 'How would you test CSRF protection in Laravel?',
                'answer_outline' => [
                    'Test a valid token succeeds for the intended state change.',
                    'Test a missing token returns 419 or the configured error shape.',
                    'Test stale token or post-logout token behavior does not mutate state.',
                    sprintf('The changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build XSS-specific interview prompts around output context and safe rendering.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function xssDefenseQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected XSS topic');

        return [
            [
                'question' => 'What is XSS and how do reflected, stored, and DOM-based XSS differ?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and explain the browser execution boundary.', $sampleTitle),
                    'XSS happens when untrusted data reaches HTML, attribute, JavaScript, URL, or DOM execution context.',
                    'Reflected XSS comes back immediately from a request, stored XSS persists in data, and DOM-based XSS happens through client-side sinks.',
                    'The first defense is context-aware output handling, not only input validation.',
                ],
            ],
            [
                'question' => 'How do you prevent XSS in Laravel Blade and JavaScript handoff?',
                'answer_outline' => [
                    'Use escaped Blade output for untrusted text.',
                    'Avoid `{!! !!}` unless the content is sanitized or fully trusted.',
                    'Serialize server data into JavaScript safely instead of concatenating strings.',
                    'Use CSP as defense-in-depth, not as the replacement for safe rendering.',
                ],
            ],
            [
                'question' => 'How would you prove the fix works?',
                'answer_outline' => [
                    'Test script tags, event-handler attributes, javascript: URLs, and rich-text payloads.',
                    'Confirm payloads render as text or are removed by the sanitizer.',
                    'Review DOM sinks such as innerHTML and unsafe template interpolation.',
                    sprintf('The changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build IDOR-specific interview prompts around object-level authorization.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function idorAccessControlQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected IDOR topic');

        return [
            [
                'question' => 'What is IDOR and why is it not solved by login alone?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and explain the object boundary.', $sampleTitle),
                    'IDOR happens when a user-controlled object ID is trusted without checking permission for that exact object.',
                    'The attacker can be authenticated; the missing control is authorization, not authentication.',
                    'Hidden buttons, UUIDs, or random IDs reduce convenience for attackers but do not replace backend object-level checks.',
                ],
            ],
            [
                'question' => 'Where should Laravel enforce object-level authorization?',
                'answer_outline' => [
                    'Scope the query by owner, tenant, team, or account before returning the model.',
                    'Call a policy or Gate for the exact object before read, update, delete, download, export, or signed URL creation.',
                    'For nested resources, verify both the parent boundary and the child object boundary.',
                    'Use 404 when object existence should be hidden and 403 when explicit denial is acceptable.',
                ],
            ],
            [
                'question' => 'How would you prove an IDOR fix works?',
                'answer_outline' => [
                    'Create owner and attacker users, and for SaaS apps use two tenants or teams.',
                    'Change only the object ID and assert the attacker receives 403 or 404.',
                    'Repeat the denial test for sibling routes such as update, delete, download, export, and nested child routes.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build Broken Authentication interview prompts around lifecycle controls.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function brokenAuthenticationQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected broken-authentication topic');

        return [
            [
                'question' => 'How do you explain broken authentication beyond a bad login form?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and explain authentication as a lifecycle.', $sampleTitle),
                    'Broken authentication can happen at login, session creation, remember-me, password reset, MFA recovery, token refresh, revocation, or logout.',
                    'The risk is that the wrong person can become, stay, or act as a user.',
                    'Authentication proves identity; authorization decides what that proven identity may access.',
                ],
            ],
            [
                'question' => 'Which Laravel controls reduce broken authentication risk?',
                'answer_outline' => [
                    'Use login throttling or RateLimiter on sensitive authentication attempts.',
                    'Regenerate the session after login and invalidate old sessions when credentials or security state changes.',
                    'Expire password reset tokens, store them safely, and reject stale or reused reset flows.',
                    'Use secure cookie flags, token rotation, token revocation, and logout invalidation for long-lived identity state.',
                ],
            ],
            [
                'question' => 'What tests and evidence would you show before merging?',
                'answer_outline' => [
                    'Test brute force throttling, stale reset token rejection, old session reuse, logout invalidation, and revoked token reuse.',
                    'Add evidence for suspicious-login logging or alerting without logging secrets or tokens.',
                    'Show the changed request, service, session config, and feature test files.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build Security Misconfiguration interview prompts around production readiness.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function securityMisconfigurationQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected security misconfiguration topic');

        return [
            [
                'question' => 'What is Security Misconfiguration and why is it common in production apps?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and define misconfiguration as unsafe runtime or deployment setup.', $sampleTitle),
                    'It includes debug mode, verbose errors, exposed secrets, default credentials, public storage, weak headers, broad CORS, and incorrect proxy trust.',
                    'It is common because settings differ between local, staging, CI, and production.',
                    'The fix is secure defaults plus environment-specific readiness checks before release.',
                ],
            ],
            [
                'question' => 'Which Laravel configuration areas would you audit first?',
                'answer_outline' => [
                    'Check APP_DEBUG, APP_ENV, config cache, exception output, and log redaction.',
                    'Check .env exposure, key rotation, storage visibility, and directory listing.',
                    'Check session cookie flags, CORS allowlists, security headers, trusted proxies, and HTTPS enforcement.',
                    'Tie each check to an owner and a rollback decision for the release.',
                ],
            ],
            [
                'question' => 'How would you prove a configuration readiness fix works?',
                'answer_outline' => [
                    'Run smoke checks that reject APP_DEBUG=true, exposed .env files, permissive CORS, and missing headers in production.',
                    'Test that debug stack traces and secrets never appear in HTTP responses or logs.',
                    'Document the expected setting per environment instead of relying on a single local .env file.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build OAuth-specific interview prompts around PKCE, callbacks, and token boundaries.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function oauthFlowQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected OAuth topic');

        return [
            [
                'question' => 'When should you use Authorization Code with PKCE instead of Implicit Flow or Client Credentials?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and classify the client type.', $sampleTitle),
                    'Authorization Code with PKCE fits browser or mobile public clients that cannot safely store a client secret.',
                    'Client Credentials fits confidential machine-to-machine clients without user delegation.',
                    'Implicit Flow should be avoided for modern browser clients because tokens can be exposed through the front channel.',
                ],
            ],
            [
                'question' => 'How does PKCE protect the authorization code exchange?',
                'answer_outline' => [
                    'The client generates a high-entropy code_verifier for one login attempt.',
                    'It sends only the derived S256 code_challenge in the authorize request.',
                    'It sends the original code_verifier only to the token endpoint with the authorization code.',
                    'A stolen authorization code is not enough without the matching verifier.',
                ],
            ],
            [
                'question' => 'How would you test an OAuth callback and token boundary?',
                'answer_outline' => [
                    'Test state mismatch, redirect URI mismatch, missing verifier, wrong verifier, expired verifier, and reused code.',
                    'Reject access_token, id_token, token_type, and expires_in values arriving in browser callback input.',
                    'Document token audience, scope, lifetime, refresh rotation, storage boundary, and log redaction.',
                    sprintf('The changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build AI type comparison interview prompts around output contracts and quality checks.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function aiTypeComparisonQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected AI comparison topic');

        return [
            [
                'question' => 'How do Predictive AI and Generative AI differ in practical product work?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and define the output contract first.', $sampleTitle),
                    'Predictive AI estimates scores, labels, forecasts, rankings, or risk decisions from existing data.',
                    'Generative AI creates new text, images, code, summaries, answers, or multimodal output from prompt and context.',
                    'The practical difference is what the product consumes and how the result is verified.',
                ],
            ],
            [
                'question' => 'How do you evaluate Predictive AI differently from Generative AI?',
                'answer_outline' => [
                    'Predictive AI needs metrics such as precision, recall, calibration, AUC, error rate, and business lift.',
                    'Generative AI needs checks for groundedness, usefulness, safety, citation quality, tests, constraints, and human review.',
                    'A single accuracy score is usually too weak for generated answers, code, or summaries.',
                    'The evaluation plan should match the output contract instead of the model brand.',
                ],
            ],
            [
                'question' => 'What failure modes should you name when comparing these two AI types?',
                'answer_outline' => [
                    'Predictive AI can fail through overfitting, drift, biased labels, bad calibration, or stale training data.',
                    'Generative AI can fail through hallucination, fabricated citations, prompt injection, unsafe code, or style drift.',
                    'Both need monitoring, but the alerts and review evidence should be different.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build database interview prompts around row locks, transactions, and contention.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function databaseLockingQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected database-locking topic');

        return [
            [
                'question' => 'What is database locking and why does it matter in real API writes?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and name the protected invariant first.', $sampleTitle),
                    'Database locking controls concurrent access so two requests cannot safely read the same old value and both write conflicting updates.',
                    'It matters for inventory, wallet balance, booking, workflow state, and any write where stale reads can create duplicate or impossible state.',
                    'The goal is correctness under concurrency, not just passing a single-request happy path.',
                ],
            ],
            [
                'question' => 'How would you use `lockForUpdate()` safely in Laravel?',
                'answer_outline' => [
                    'Wrap the critical read, validation, and write in `DB::transaction()`.',
                    'Call `lockForUpdate()` on a selective, indexed row lookup before checking stock, balance, or state.',
                    'Keep external API calls, emails, queues, sleeps, and slow computation outside the transaction.',
                    'Use consistent lock ordering when multiple rows are locked so deadlock risk stays lower.',
                ],
            ],
            [
                'question' => 'What evidence would you require before merging a locking fix?',
                'answer_outline' => [
                    'A concurrent-request fixture proves the previous race cannot oversell inventory, double-spend balance, or move state twice.',
                    'Failure-path tests cover insufficient stock, stale state, timeout, or deadlock retry behavior.',
                    'Review notes identify row lock versus table lock risk, hot-row contention, indexed lookup requirements, and lock-wait monitoring.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }

    /**
     * Build database interview prompts around covering indexes and heap-fetch evidence.
     *
     * @return array<int, array{question: string, answer_outline: array<int, string>}>
     */
    private function coveringIndexQuestions(array $artifact): array
    {
        $sourceCoverage = $artifact['portfolio']['source_coverage'];
        $sampleTitle = (string) ($sourceCoverage[0]['title'] ?? 'the selected covering-index topic');

        return [
            [
                'question' => 'Why can an indexed PostgreSQL query still be slow because of heap fetches?',
                'answer_outline' => [
                    sprintf('I start from the source topic `%s` and inspect the query plan, not only the index definition.', $sampleTitle),
                    'A normal index scan can find matching index entries but still visit the table heap to read projected columns or confirm row visibility.',
                    'Those heap visits become expensive random IO when the query touches many rows or pages.',
                    'The evidence should come from EXPLAIN (ANALYZE, BUFFERS), including Heap Fetches and buffer reads.',
                ],
            ],
            [
                'question' => 'How does a covering index with INCLUDE change the query plan?',
                'answer_outline' => [
                    'Key columns support filtering and ordering, while INCLUDE columns store extra returned values in the index payload.',
                    'When all selected columns are available from the index, PostgreSQL can choose Index Only Scan.',
                    'Index Only Scan still depends on the visibility map, so VACUUM and autovacuum health are part of the implementation story.',
                    'INCLUDE should be limited to the hot query columns because every extra byte increases index size and write cost.',
                ],
            ],
            [
                'question' => 'What review evidence would you require before merging a covering-index optimization?',
                'answer_outline' => [
                    'Before and after EXPLAIN output shows reduced Heap Fetches and the expected Index Only Scan path.',
                    'The migration names key columns, INCLUDE columns, and why each included column is needed by the hot query.',
                    'The rollout note covers index size, bloat risk, write overhead, VACUUM visibility-map health, and rollback.',
                    sprintf('Cite the changed files and verification commands from `%s`.', $artifact['commit_plan']['branch']),
                ],
            ],
        ];
    }
}
