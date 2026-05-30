<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class DatabaseLockingPlanWorkbenchTest extends TestCase
{
    /**
     * The database-locking workbench renders the transaction planning UI.
     */
    public function test_database_locking_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/database-locking-plan');

        $response
            ->assertOk()
            ->assertSee('Database Locking Plan Workbench')
            ->assertSee('POST /api/practice/database-locking-plan')
            ->assertSee('DatabaseLockingPlanService')
            ->assertSee('Inventory reservation')
            ->assertSee('Wallet debit')
            ->assertSee('Readiness')
            ->assertSee('Blockers')
            ->assertSee('Failure mode matrix')
            ->assertSee('Implementation blueprint')
            ->assertSee('Strategy decision table')
            ->assertSee('Plan locking');
    }

    /**
     * The database-locking API returns transaction, lock, test, and observability guidance.
     */
    public function test_database_locking_plan_api_returns_inventory_plan(): void
    {
        $response = $this->postJson('/api/practice/database-locking-plan', [
            'scenario_name' => 'Inventory Reservation',
            'invariant' => 'inventory',
            'resource_type' => 'product',
            'concurrent_requests' => 25,
            'touches_multiple_rows' => false,
            'has_external_side_effect' => false,
            'expected_failure' => 'reject',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.scenario', 'Inventory Reservation')
            ->assertJsonPath('data.protected_invariant', 'Stock cannot become negative and only one reservation can consume the last unit.')
            ->assertJsonPath('data.recommended_lock', 'row-level lock with `lockForUpdate()` inside `DB::transaction()`')
            ->assertJsonPath('data.transaction_boundary.steps.0', 'Start `DB::transaction()` at the service layer after validation and authorization have completed.')
            ->assertJsonPath('data.lock_plan.target', 'Lock the smallest product row set needed to protect the invariant.')
            ->assertJsonPath('data.failure_handling.behavior', 'Return a domain error such as out of stock, insufficient balance, slot unavailable, or stale state.')
            ->assertJsonPath('data.failure_mode_matrix.0.mode', 'lost update')
            ->assertJsonPath('data.failure_mode_matrix.0.guardrail', 'Take `lockForUpdate()` before checking and mutating the protected value.')
            ->assertJsonPath('data.failure_mode_matrix.2.metric', 'database_lock_timeout_total')
            ->assertJsonPath('data.implementation_blueprint.service_class', 'InventoryReservationService')
            ->assertJsonPath('data.implementation_blueprint.service_method', 'handleReject')
            ->assertJsonPath('data.implementation_blueprint.files.0.path', 'app/Services/Practice/InventoryReservationService.php')
            ->assertJsonPath('data.implementation_blueprint.service_skeleton.7', '                ->lockForUpdate()')
            ->assertJsonPath('data.implementation_blueprint.migration_note', 'Index the locked product lookup before release; broad predicates can turn a row-lock lesson into table-level contention.')
            ->assertJsonPath('data.strategy_decision_table.0.strategy', 'pessimistic row lock')
            ->assertJsonPath('data.strategy_decision_table.1.strategy', 'unique constraint')
            ->assertJsonPath('data.strategy_decision_table.2.decision_for_input', 'not enough by itself for this hot-row input; use it only with a clear retry UX')
            ->assertJsonPath('data.risk_assessment.level', 'medium')
            ->assertJsonPath('data.risk_assessment.reasons.0', 'High concurrent request count can create lock waits and hot-row contention.')
            ->assertJsonPath('data.readiness_scorecard.score', 100)
            ->assertJsonPath('data.readiness_scorecard.verdict', 'ready for implementation review')
            ->assertJsonPath('data.readiness_scorecard.dimensions.1.name', 'Transaction-bound row lock')
            ->assertJsonPath('data.readiness_scorecard.blockers', [])
            ->assertJsonPath('data.review_checklist.1', '`lockForUpdate()` is inside the same `DB::transaction()` as read, validation, and write.')
            ->assertJsonPath('data.test_plan.0', 'Create two requests targeting the same product row and prove only one succeeds when the invariant would be broken.')
            ->assertJsonPath('data.observability_plan.metrics.0', 'database_lock_wait_ms')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter DatabaseLockingPlanWorkbenchTest');
    }

    /**
     * Multi-row external-side-effect scenarios are high risk and require ordering.
     */
    public function test_database_locking_plan_api_flags_high_risk_multi_row_side_effects(): void
    {
        $response = $this->postJson('/api/practice/database-locking-plan', [
            'scenario_name' => 'Wallet Debit',
            'invariant' => 'balance',
            'resource_type' => 'account',
            'concurrent_requests' => 40,
            'touches_multiple_rows' => true,
            'has_external_side_effect' => true,
            'expected_failure' => 'retry',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.risk_assessment.level', 'high')
            ->assertJsonPath('data.readiness_scorecard.score', 95)
            ->assertJsonPath('data.readiness_scorecard.verdict', 'needs focused review')
            ->assertJsonPath('data.readiness_scorecard.blockers.0', 'Move external side effects after commit or behind an after-commit job before release.')
            ->assertJsonPath('data.readiness_scorecard.blockers.1', 'Document stable lock ordering for every multi-row code path.')
            ->assertJsonPath('data.readiness_scorecard.blockers.2', 'Add bounded retry, jitter, and idempotency proof for transient deadlock or timeout handling.')
            ->assertJsonPath('data.readiness_scorecard.blockers.3', 'Capture lock-wait and deadlock evidence before promoting this high-risk scenario.')
            ->assertJsonPath('data.failure_mode_matrix.3.mode', 'deadlock')
            ->assertJsonPath('data.failure_mode_matrix.4.mode', 'duplicated side effect')
            ->assertJsonPath('data.failure_mode_matrix.5.mode', 'unsafe retry')
            ->assertJsonPath('data.implementation_blueprint.service_class', 'WalletDebitService')
            ->assertJsonPath('data.implementation_blueprint.files.1.path', 'tests/Feature/WalletDebitConcurrencyTest.php')
            ->assertJsonPath('data.implementation_blueprint.pull_request_notes.2', 'Document stable row ordering and include an opposite-order replay note.')
            ->assertJsonPath('data.implementation_blueprint.pull_request_notes.3', 'Show that side effects run after commit and are not repeated by retries.')
            ->assertJsonPath('data.strategy_decision_table.3.decision_for_input', 'useful for moving slow side effects after commit and out of the locked section')
            ->assertJsonPath('data.lock_plan.ordering', 'Sort IDs and lock rows in the same order in every code path.')
            ->assertJsonPath('data.lock_plan.critical_section', 'Move the side effect after commit; the locked section should contain only database reads and writes.')
            ->assertJsonPath('data.failure_handling.retry_policy', 'Use small bounded retry count, jitter, and idempotency checks.');
    }

    /**
     * Invalid database-locking payloads return validation errors.
     */
    public function test_database_locking_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/database-locking-plan', [
            'scenario_name' => '<bad>',
            'invariant' => 'random',
            'resource_type' => 'row',
            'concurrent_requests' => 1,
            'touches_multiple_rows' => 'maybe',
            'has_external_side_effect' => 'maybe',
            'expected_failure' => 'ignore',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'scenario_name',
                'invariant',
                'resource_type',
                'concurrent_requests',
                'touches_multiple_rows',
                'has_external_side_effect',
                'expected_failure',
            ]);
    }
}
