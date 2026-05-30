<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

/**
 * Builds transaction-bound database-locking plans for Laravel concurrency practice.
 */
final class DatabaseLockingPlanService
{
    /**
     * Build a database-locking plan for one concurrent write scenario.
     *
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $risk = $this->riskAssessment($input);

        return [
            'scenario' => Str::headline($input['scenario_name']),
            'protected_invariant' => $this->invariantFor($input['invariant']),
            'recommended_lock' => 'row-level lock with `lockForUpdate()` inside `DB::transaction()`',
            'transaction_boundary' => $this->transactionBoundary($input),
            'lock_plan' => $this->lockPlan($input),
            'failure_handling' => $this->failureHandling($input),
            'failure_mode_matrix' => $this->failureModeMatrix($input),
            'implementation_blueprint' => $this->implementationBlueprint($input),
            'strategy_decision_table' => $this->strategyDecisionTable($input),
            'risk_assessment' => $risk,
            'readiness_scorecard' => $this->readinessScorecard($input, $risk),
            'review_checklist' => $this->reviewChecklist($input),
            'test_plan' => $this->testPlan($input),
            'observability_plan' => $this->observabilityPlan($input, $risk['level']),
            'interview_answer' => $this->interviewAnswer($input),
            'commands' => [
                'php artisan test --filter DatabaseLockingPlanWorkbenchTest',
                'php artisan route:list --path=database-locking-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Compare concurrency-control strategies before defaulting to a row lock.
     *
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @return array<int, array{strategy: string, use_when: string, avoid_when: string, laravel_shape: string, decision_for_input: string}>
     */
    private function strategyDecisionTable(array $input): array
    {
        return [
            [
                'strategy' => 'pessimistic row lock',
                'use_when' => 'A current row value must be read, validated, and changed atomically under contention.',
                'avoid_when' => 'The invariant can be enforced by a unique key or append-only command log.',
                'laravel_shape' => '`DB::transaction()` plus a selective `lockForUpdate()` query.',
                'decision_for_input' => 'recommended because this plan protects '.$this->invariantFor($input['invariant']),
            ],
            [
                'strategy' => 'unique constraint',
                'use_when' => 'The invariant is uniqueness, such as one booking per slot or one idempotency key per command.',
                'avoid_when' => 'The write depends on comparing and mutating a changing numeric value.',
                'laravel_shape' => 'Migration unique index plus catching duplicate-key domain errors.',
                'decision_for_input' => $input['invariant'] === 'booking'
                    ? 'consider as a primary or backup guardrail for booking uniqueness'
                    : 'use as a supporting guardrail only if the domain has a unique command or reservation key',
            ],
            [
                'strategy' => 'optimistic version check',
                'use_when' => 'Conflicts are rare and the client can retry after seeing stale state.',
                'avoid_when' => 'Oversell, double-spend, or terminal workflow transitions must fail deterministically under high contention.',
                'laravel_shape' => 'Version column in the `where` clause, then check affected rows.',
                'decision_for_input' => $input['concurrent_requests'] >= 20
                    ? 'not enough by itself for this hot-row input; use it only with a clear retry UX'
                    : 'viable if stale writes can be rejected and retried by the caller',
            ],
            [
                'strategy' => 'serialized queue',
                'use_when' => 'The business can accept asynchronous processing and wants one worker per resource key.',
                'avoid_when' => 'The caller needs immediate success or rejection in the request path.',
                'laravel_shape' => 'Dispatch jobs keyed by resource ID and process them through a single concurrency lane.',
                'decision_for_input' => $input['has_external_side_effect']
                    ? 'useful for moving slow side effects after commit and out of the locked section'
                    : 'optional if synchronous transaction latency is acceptable',
            ],
        ];
    }

    /**
     * Build implementation artifacts that turn the plan into Laravel service and test work.
     *
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @return array{service_class: string, service_method: string, files: array<int, array{path: string, purpose: string}>, service_skeleton: array<int, string>, test_skeleton: array<int, string>, migration_note: string, pull_request_notes: array<int, string>}
     */
    private function implementationBlueprint(array $input): array
    {
        $baseName = Str::studly($input['scenario_name']);
        $serviceClass = "{$baseName}Service";
        $method = 'handle'.Str::studly($input['expected_failure']);
        $resourceVariable = Str::camel($input['resource_type']);

        return [
            'service_class' => $serviceClass,
            'service_method' => $method,
            'files' => [
                [
                    'path' => "app/Services/Practice/{$serviceClass}.php",
                    'purpose' => 'Own the transaction boundary, locked lookup, invariant check, state mutation, and after-commit side effects.',
                ],
                [
                    'path' => "tests/Feature/{$baseName}ConcurrencyTest.php",
                    'purpose' => 'Replay concurrent requests against one protected row and prove the invariant survives.',
                ],
                [
                    'path' => 'database/migrations/*_add_locking_supporting_index.php',
                    'purpose' => 'Add or verify the selective index used by the locked lookup.',
                ],
            ],
            'service_skeleton' => $this->serviceSkeleton($input, $serviceClass, $method, $resourceVariable),
            'test_skeleton' => $this->testSkeleton($input, $baseName),
            'migration_note' => "Index the locked {$input['resource_type']} lookup before release; broad predicates can turn a row-lock lesson into table-level contention.",
            'pull_request_notes' => [
                'Name the invariant at the top of the PR and link the concurrency test.',
                'Show the locked query, the supporting index, and the final row state after replay.',
                $input['touches_multiple_rows']
                    ? 'Document stable row ordering and include an opposite-order replay note.'
                    : 'Document why one locked row is enough for this invariant.',
                $input['has_external_side_effect']
                    ? 'Show that side effects run after commit and are not repeated by retries.'
                    : 'State that the transaction contains no mail, HTTP, file IO, or queue dispatch.',
            ],
        ];
    }

    /**
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @return array<int, string>
     */
    private function serviceSkeleton(array $input, string $serviceClass, string $method, string $resourceVariable): array
    {
        return [
            "final class {$serviceClass}",
            '{',
            "    public function {$method}(int \${$resourceVariable}Id): void",
            '    {',
            '        DB::transaction(function () use ($'.$resourceVariable.'Id): void {',
            "            \${$resourceVariable} = {$this->modelNameFor($input['resource_type'])}::query()",
            '                ->whereKey($'.$resourceVariable.'Id)',
            '                ->lockForUpdate()',
            '                ->firstOrFail();',
            '',
            '            // Re-check the protected invariant while the row lock is held.',
            '            // Mutate the row and create audit/order records before commit.',
            '        });',
            '    }',
            '}',
        ];
    }

    /**
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @return array<int, string>
     */
    private function testSkeleton(array $input, string $baseName): array
    {
        return [
            "final class {$baseName}ConcurrencyTest extends TestCase",
            '{',
            '    public function test_concurrent_requests_preserve_the_invariant(): void',
            '    {',
            "        // Arrange one {$input['resource_type']} row at the edge of the invariant.",
            '        // Fire two requests or service calls that target the same row.',
            '        // Assert one success, one expected failure, and a valid final state.',
            '    }',
            '}',
        ];
    }

    private function modelNameFor(string $resourceType): string
    {
        return match ($resourceType) {
            'account' => 'Account',
            'booking_slot' => 'BookingSlot',
            'workflow_item' => 'WorkflowItem',
            default => 'Product',
        };
    }

    /**
     * Map common concurrency failures to controls, tests, and observability evidence.
     *
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @return array<int, array{mode: string, trigger: string, guardrail: string, test_evidence: string, metric: string}>
     */
    private function failureModeMatrix(array $input): array
    {
        $matrix = [
            [
                'mode' => 'lost update',
                'trigger' => "Two requests read the same {$input['resource_type']} value before either write commits.",
                'guardrail' => 'Take `lockForUpdate()` before checking and mutating the protected value.',
                'test_evidence' => 'Concurrent replay proves one request succeeds and the other returns the expected domain failure.',
                'metric' => 'database_concurrency_rejected_total',
            ],
            [
                'mode' => 'stale invariant check',
                'trigger' => 'The code reads stock, balance, slot, or workflow state before the locked query.',
                'guardrail' => 'Move the protected read inside the same `DB::transaction()` as the locked query.',
                'test_evidence' => 'Regression test fails if the invariant check is moved before `lockForUpdate()`.',
                'metric' => "{$input['resource_type']}_hot_row_attempts_total",
            ],
            [
                'mode' => 'lock timeout',
                'trigger' => 'A transaction holds the row lock long enough that another request waits too long.',
                'guardrail' => 'Keep the critical section small and remove slow non-database work from the lock window.',
                'test_evidence' => 'Load or integration test records bounded wait behavior for the hot row.',
                'metric' => 'database_lock_timeout_total',
            ],
        ];

        if ($input['touches_multiple_rows']) {
            $matrix[] = [
                'mode' => 'deadlock',
                'trigger' => 'Two code paths lock the same rows in different orders.',
                'guardrail' => 'Sort IDs and lock rows in the same order in every path.',
                'test_evidence' => 'Opposite-order replay proves the service uses stable lock ordering.',
                'metric' => 'database_deadlock_retries_total',
            ];
        }

        if ($input['has_external_side_effect']) {
            $matrix[] = [
                'mode' => 'duplicated side effect',
                'trigger' => 'Mail, HTTP calls, file IO, or queue dispatch happens before commit or inside retryable work.',
                'guardrail' => 'Move side effects after commit and make retryable work idempotent.',
                'test_evidence' => 'Test proves rejected or retried writes do not send duplicate external effects.',
                'metric' => 'database_side_effect_after_commit_total',
            ];
        }

        if ($input['expected_failure'] === 'retry') {
            $matrix[] = [
                'mode' => 'unsafe retry',
                'trigger' => 'Retry logic repeats a non-idempotent write after a deadlock or transient timeout.',
                'guardrail' => 'Use bounded attempts, jitter, and idempotency keys only for transient failures.',
                'test_evidence' => 'Retry test proves one logical operation creates one durable result.',
                'metric' => 'database_deadlock_retries_total',
            ];
        }

        return $matrix;
    }

    /**
     * @param  array<int, array{mode: string, trigger: string, guardrail: string, test_evidence: string, metric: string}>  $matrix
     * @return array<int, string>
     */
    private function matrixReviewPrompts(array $matrix): array
    {
        return collect($matrix)
            ->map(fn (array $row): string => "For {$row['mode']}, show the guardrail, test evidence, and metric before review.")
            ->values()
            ->all();
    }

    /**
     * Convert the locking plan shape into a 100-point release-readiness scorecard.
     *
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @param  array{level: string, score: int, reasons: array<int, string>, mitigations: array<int, string>}  $risk
     * @return array{score: int, verdict: string, dimensions: array<int, array{name: string, points: int, evidence: string}>, blockers: array<int, string>, next_actions: array<int, string>}
     */
    private function readinessScorecard(array $input, array $risk): array
    {
        $dimensions = [
            [
                'name' => 'Invariant named before code',
                'points' => 20,
                'evidence' => $this->invariantFor($input['invariant']),
            ],
            [
                'name' => 'Transaction-bound row lock',
                'points' => 25,
                'evidence' => '`DB::transaction()` wraps the locked read, invariant check, and write.',
            ],
            [
                'name' => 'Narrow indexed lock scope',
                'points' => 20,
                'evidence' => "The {$input['resource_type']} lookup is scoped to the smallest indexed row set.",
            ],
            [
                'name' => 'Concurrent failure test',
                'points' => $input['concurrent_requests'] >= 2 ? 20 : 0,
                'evidence' => "Replay at least {$input['concurrent_requests']} requests against the same protected row.",
            ],
            [
                'name' => 'Operational failure handling',
                'points' => $risk['level'] === 'high' ? 10 : 15,
                'evidence' => $risk['level'] === 'high'
                    ? 'High-risk locking needs extra proof for deadlocks, retries, side effects, and lock waits.'
                    : 'Risk is bounded by short transactions, clear rejection behavior, and monitoring.',
            ],
        ];

        $score = (int) collect($dimensions)->sum('points');
        $blockers = $this->readinessBlockers($input, $risk);

        return [
            'score' => $score,
            'verdict' => match (true) {
                $score >= 90 && $blockers === [] => 'ready for implementation review',
                $score >= 75 => 'needs focused review',
                default => 'not ready',
            },
            'dimensions' => $dimensions,
            'blockers' => $blockers,
            'next_actions' => $this->readinessNextActions($input, $risk),
        ];
    }

    /**
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @param  array{level: string, score: int, reasons: array<int, string>, mitigations: array<int, string>}  $risk
     * @return array<int, string>
     */
    private function readinessBlockers(array $input, array $risk): array
    {
        $blockers = [];

        if ($input['has_external_side_effect']) {
            $blockers[] = 'Move external side effects after commit or behind an after-commit job before release.';
        }

        if ($input['touches_multiple_rows']) {
            $blockers[] = 'Document stable lock ordering for every multi-row code path.';
        }

        if ($input['expected_failure'] === 'retry') {
            $blockers[] = 'Add bounded retry, jitter, and idempotency proof for transient deadlock or timeout handling.';
        }

        if ($risk['level'] === 'high') {
            $blockers[] = 'Capture lock-wait and deadlock evidence before promoting this high-risk scenario.';
        }

        return array_values(array_unique($blockers));
    }

    /**
     * @param  array{scenario_name: string, invariant: string, resource_type: string, concurrent_requests: int, touches_multiple_rows: bool, has_external_side_effect: bool, expected_failure: string}  $input
     * @param  array{level: string, score: int, reasons: array<int, string>, mitigations: array<int, string>}  $risk
     * @return array<int, string>
     */
    private function readinessNextActions(array $input, array $risk): array
    {
        return [
            "Write one concurrency test that targets the same {$input['resource_type']} row from two requests.",
            'Attach the locked query and explain which index supports it.',
            'Compare row lock, unique constraint, optimistic version, and serialized queue before committing to the final design.',
            ...$this->matrixReviewPrompts($this->failureModeMatrix($input)),
            $input['touches_multiple_rows']
                ? 'Sort row IDs before locking and add a regression test for opposite request ordering.'
                : 'Keep the lock scope to one row unless the invariant requires more.',
            $input['has_external_side_effect']
                ? 'Move mail, HTTP calls, files, or queue dispatch after commit.'
                : 'Keep the transaction free of slow non-database work.',
            $risk['level'] === 'high'
                ? 'Add lock-wait, deadlock retry, and timeout metrics to the release evidence.'
                : 'Record the low or medium risk rationale in the pull request.',
        ];
    }

    /**
     * Score operational locking risk from contention and transaction shape.
     *
     * @return array{level: string, score: int, reasons: array<int, string>, mitigations: array<int, string>}
     */
    private function riskAssessment(array $input): array
    {
        $score = 20;
        $reasons = [];

        if ($input['concurrent_requests'] >= 20) {
            $score += 25;
            $reasons[] = 'High concurrent request count can create lock waits and hot-row contention.';
        }

        if ($input['touches_multiple_rows']) {
            $score += 20;
            $reasons[] = 'Locking multiple rows raises deadlock risk unless lock ordering is consistent.';
        }

        if ($input['has_external_side_effect']) {
            $score += 25;
            $reasons[] = 'External side effects inside the transaction can hold locks while waiting on slow systems.';
        }

        if ($input['expected_failure'] === 'retry') {
            $score += 10;
            $reasons[] = 'Retry behavior needs idempotency and bounded attempts.';
        }

        $score = min(100, $score);

        return [
            'level' => match (true) {
                $score >= 70 => 'high',
                $score >= 40 => 'medium',
                default => 'low',
            },
            'score' => $score,
            'reasons' => $reasons === [] ? ['Single-row transaction with no external side effect stays low risk for this practice input.'] : $reasons,
            'mitigations' => [
                'Lock by primary key or another selective indexed predicate.',
                'Keep the transaction small: read, validate, write, commit.',
                'Move HTTP calls, mail, files, and queue dispatch outside the locked section.',
                'Add bounded retry only for deadlocks or transient lock timeouts.',
            ],
        ];
    }

    /**
     * Return the invariant statement for the selected failure class.
     */
    private function invariantFor(string $invariant): string
    {
        return match ($invariant) {
            'inventory' => 'Stock cannot become negative and only one reservation can consume the last unit.',
            'balance' => 'Balance cannot be spent twice and every debit must see the latest committed balance.',
            'booking' => 'One slot cannot be booked by more than one successful request.',
            'workflow-state' => 'A workflow row cannot move through the same terminal transition twice.',
            default => 'The protected row must not be changed from stale state by concurrent requests.',
        };
    }

    /**
     * @return array{steps: array<int, string>, anti_patterns: array<int, string>}
     */
    private function transactionBoundary(array $input): array
    {
        return [
            'steps' => [
                'Start `DB::transaction()` at the service layer after validation and authorization have completed.',
                "Load the {$input['resource_type']} row with `lockForUpdate()` through an indexed lookup.",
                'Validate the protected invariant while holding the row lock.',
                'Write the new state, create the audit or order row, and commit before doing slow side effects.',
            ],
            'anti_patterns' => [
                'Calling `lockForUpdate()` outside a transaction.',
                'Reading the protected value before the locked query.',
                'Sending email, HTTP requests, file IO, or queue work before commit.',
                'Catching every exception and retrying non-idempotent writes blindly.',
            ],
        ];
    }

    /**
     * @return array{target: string, query_shape: string, ordering: string, critical_section: string}
     */
    private function lockPlan(array $input): array
    {
        return [
            'target' => "Lock the smallest {$input['resource_type']} row set needed to protect the invariant.",
            'query_shape' => 'Use primary key, tenant key plus ID, account ID, product ID, or another indexed predicate.',
            'ordering' => $input['touches_multiple_rows']
                ? 'Sort IDs and lock rows in the same order in every code path.'
                : 'Single-row lock does not need multi-row ordering, but still needs a narrow indexed lookup.',
            'critical_section' => $input['has_external_side_effect']
                ? 'Move the side effect after commit; the locked section should contain only database reads and writes.'
                : 'Keep the locked section short and free of slow non-database work.',
        ];
    }

    /**
     * @return array{expected_failure: string, behavior: string, retry_policy: string}
     */
    private function failureHandling(array $input): array
    {
        return [
            'expected_failure' => $input['expected_failure'],
            'behavior' => match ($input['expected_failure']) {
                'reject' => 'Return a domain error such as out of stock, insufficient balance, slot unavailable, or stale state.',
                'retry' => 'Retry only deadlock or transient timeout failures with bounded attempts and idempotency keys.',
                default => 'Fail closed and record enough context to replay the scenario safely.',
            },
            'retry_policy' => $input['expected_failure'] === 'retry'
                ? 'Use small bounded retry count, jitter, and idempotency checks.'
                : 'Do not retry business-rule failures; ask the caller to refresh state.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function reviewChecklist(array $input): array
    {
        return [
            'The invariant is written in the review before code is approved.',
            '`lockForUpdate()` is inside the same `DB::transaction()` as read, validation, and write.',
            'The lock query is indexed and scoped by tenant, account, product, booking slot, or workflow owner where applicable.',
            $input['touches_multiple_rows'] ? 'Multiple rows are locked in a stable order.' : 'Single-row lock scope is documented.',
            $input['has_external_side_effect'] ? 'External side effects run after commit.' : 'No external side effect is performed while the lock is held.',
            'Deadlock, timeout, and lock-wait monitoring are documented.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function testPlan(array $input): array
    {
        return [
            "Create two requests targeting the same {$input['resource_type']} row and prove only one succeeds when the invariant would be broken.",
            'Assert the rejected path returns the expected domain error and does not create a partial order, debit, booking, or transition.',
            'Assert the final row state remains valid after both attempts complete.',
            $input['touches_multiple_rows'] ? 'Add a multi-row test that locks rows in stable sorted order.' : 'Add a single-row replay note for the locked lookup.',
            $input['has_external_side_effect'] ? 'Assert side effects happen after commit or through an after-commit job.' : 'Assert the service does not call external dependencies inside the transaction.',
        ];
    }

    /**
     * @return array{metrics: array<int, string>, log_fields: array<int, string>, alerts: array<int, string>, runbook: array<int, string>}
     */
    private function observabilityPlan(array $input, string $riskLevel): array
    {
        return [
            'metrics' => [
                'database_lock_wait_ms',
                'database_deadlock_retries_total',
                'database_lock_timeout_total',
                'database_concurrency_rejected_total',
                "{$input['resource_type']}_hot_row_attempts_total",
            ],
            'log_fields' => [
                'scenario',
                'resource_type',
                'resource_id',
                'invariant',
                'attempt',
                'lock_wait_ms',
                'failure_reason',
            ],
            'alerts' => [
                "Alert when database_lock_wait_ms p95 crosses the {$riskLevel} risk threshold.",
                'Alert when deadlock retries or lock timeouts increase after deploy.',
            ],
            'runbook' => [
                'Confirm whether waits are tied to one hot row or a broad lock query.',
                'Inspect transaction duration and remove slow side effects from the lock window.',
                'Check index usage for the locked predicate.',
                'For repeated deadlocks, verify stable lock ordering and retry limits.',
            ],
        ];
    }

    private function interviewAnswer(array $input): string
    {
        return sprintf(
            'I would protect %s by naming the invariant first, then putting the critical read, validation, and write in DB::transaction(). I would load the %s row with lockForUpdate() through an indexed lookup, keep slow side effects outside the transaction, test concurrent replay, and monitor lock waits, deadlocks, and timeouts.',
            $this->invariantFor($input['invariant']),
            $input['resource_type'],
        );
    }
}
