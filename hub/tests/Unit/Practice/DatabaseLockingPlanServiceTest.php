<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\DatabaseLockingPlanService;
use PHPUnit\Framework\TestCase;

final class DatabaseLockingPlanServiceTest extends TestCase
{
    /**
     * Low-complexity single-row locking plans receive a ready scorecard.
     */
    public function test_single_row_locking_plan_is_ready_for_review(): void
    {
        $plan = (new DatabaseLockingPlanService)->plan([
            'scenario_name' => 'Booking Slot',
            'invariant' => 'booking',
            'resource_type' => 'booking_slot',
            'concurrent_requests' => 12,
            'touches_multiple_rows' => false,
            'has_external_side_effect' => false,
            'expected_failure' => 'fail-closed',
        ]);

        $this->assertSame('Booking Slot', $plan['scenario']);
        $this->assertSame('low', $plan['risk_assessment']['level']);
        $this->assertSame(100, $plan['readiness_scorecard']['score']);
        $this->assertSame('ready for implementation review', $plan['readiness_scorecard']['verdict']);
        $this->assertSame([], $plan['readiness_scorecard']['blockers']);
        $this->assertSame(['lost update', 'stale invariant check', 'lock timeout'], array_column($plan['failure_mode_matrix'], 'mode'));
        $this->assertSame('BookingSlotService', $plan['implementation_blueprint']['service_class']);
        $this->assertSame('handleFailClosed', $plan['implementation_blueprint']['service_method']);
        $this->assertContains('                ->lockForUpdate()', $plan['implementation_blueprint']['service_skeleton']);
        $this->assertSame('database/migrations/*_add_locking_supporting_index.php', $plan['implementation_blueprint']['files'][2]['path']);
        $this->assertSame('unique constraint', $plan['strategy_decision_table'][1]['strategy']);
        $this->assertSame('consider as a primary or backup guardrail for booking uniqueness', $plan['strategy_decision_table'][1]['decision_for_input']);
        $this->assertContains('Compare row lock, unique constraint, optimistic version, and serialized queue before committing to the final design.', $plan['readiness_scorecard']['next_actions']);
        $this->assertSame('Single-row lock scope is documented.', $plan['review_checklist'][3]);
    }

    /**
     * High-contention multi-row plans surface blockers before release.
     */
    public function test_high_risk_multi_row_plan_requires_focused_review(): void
    {
        $plan = (new DatabaseLockingPlanService)->plan([
            'scenario_name' => 'Wallet Debit',
            'invariant' => 'balance',
            'resource_type' => 'account',
            'concurrent_requests' => 40,
            'touches_multiple_rows' => true,
            'has_external_side_effect' => true,
            'expected_failure' => 'retry',
        ]);

        $this->assertSame('high', $plan['risk_assessment']['level']);
        $this->assertSame(95, $plan['readiness_scorecard']['score']);
        $this->assertSame('needs focused review', $plan['readiness_scorecard']['verdict']);
        $this->assertSame(
            ['lost update', 'stale invariant check', 'lock timeout', 'deadlock', 'duplicated side effect', 'unsafe retry'],
            array_column($plan['failure_mode_matrix'], 'mode'),
        );
        $this->assertContains('Document stable lock ordering for every multi-row code path.', $plan['readiness_scorecard']['blockers']);
        $this->assertContains('For deadlock, show the guardrail, test evidence, and metric before review.', $plan['readiness_scorecard']['next_actions']);
        $this->assertContains('Add lock-wait, deadlock retry, and timeout metrics to the release evidence.', $plan['readiness_scorecard']['next_actions']);
        $this->assertSame('WalletDebitService', $plan['implementation_blueprint']['service_class']);
        $this->assertSame('            $account = Account::query()', $plan['implementation_blueprint']['service_skeleton'][5]);
        $this->assertSame('tests/Feature/WalletDebitConcurrencyTest.php', $plan['implementation_blueprint']['files'][1]['path']);
        $this->assertSame('not enough by itself for this hot-row input; use it only with a clear retry UX', $plan['strategy_decision_table'][2]['decision_for_input']);
        $this->assertSame('useful for moving slow side effects after commit and out of the locked section', $plan['strategy_decision_table'][3]['decision_for_input']);
    }
}
