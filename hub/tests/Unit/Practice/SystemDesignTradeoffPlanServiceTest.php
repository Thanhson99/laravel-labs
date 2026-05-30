<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\SystemDesignTradeoffPlanService;
use PHPUnit\Framework\TestCase;

final class SystemDesignTradeoffPlanServiceTest extends TestCase
{
    /**
     * Real-time notification systems surface WebSocket and event-stream tradeoffs.
     */
    public function test_real_time_notification_plan_explains_websocket_tradeoff(): void
    {
        $plan = (new SystemDesignTradeoffPlanService)->plan([
            'scenario' => 'notification-10m',
            'latency_requirement' => 'real-time',
            'failure_impact' => 'high',
            'consistency_need' => 'eventual',
            'team_maturity' => 'platform',
            'operational_capacity' => 'high',
            'current_constraint' => 'greenfield',
            'target_level' => 'l7',
            'candidate_answer' => 'I would first clarify latency, platform, outage impact and requirements. I choose WebSocket but the tradeoff is operational cost. I reject polling instead because it misses real-time needs. The platform team can operate it, I would monitor fanout lag on a dashboard, revisit when the metric breaks the SLO, and keep rollback for gateway failure and backpressure.',
        ]);

        $this->assertSame('websocket_event_stream', $plan['recommendation']['style']);
        $this->assertStringContainsString('The price is higher operational complexity', $plan['tradeoff_statement']);
        $this->assertStringContainsString('Before drawing the notification system', $plan['interview_answer']);
        $this->assertContains('fanout_lag_ms', $plan['operating_model']['metrics']);
        $this->assertStringContainsString('different gateway node', $plan['interviewer_followups'][3]);
        $this->assertSame('What breaks first in production?', $plan['interviewer_simulation'][1]['interviewer']);
        $this->assertStringContainsString('fanout_lag_ms', $plan['interviewer_simulation'][1]['strong_response']);
        $this->assertStringContainsString('review triggers', $plan['rewrite_practice']['stronger_answer']);
        $this->assertSame(20, $plan['answer_scorecard']['max_score']);
        $this->assertSame('Tool-first answer', $plan['anti_patterns'][0]['pattern']);
        $this->assertContains('Mentions connection ownership, fan-out path, and backpressure.', $plan['green_flags']);
        $this->assertSame('Phase 1: smallest safe design', $plan['architecture_evolution_path'][0]['phase']);
        $this->assertSame('fanout_lag_ms', $plan['answer_contract']['filled_example']['first_metric']);
        $this->assertSame('Failure mode', $plan['review_checklist'][3]['item']);
        $this->assertStringContainsString('Interview me on System Design', $plan['practice_prompt']['prompt']);
        $this->assertSame('weak', $plan['calibration_examples'][0]['level']);
        $this->assertStringContainsString('I would not draw first', $plan['one_minute_answer']);
        $this->assertStringContainsString('First I would clarify', $plan['two_minute_answer']);
        $this->assertSame('What is the first move in a System Design interview?', $plan['drill_cards'][0]['front']);
        $this->assertSame('Is real-time delivery a hard requirement?', $plan['decision_tree'][0]['question']);
        $this->assertSame('Change latency from real-time to delayed by 30 seconds.', $plan['scenario_variations'][0]['change']);
        $this->assertStringContainsString('Senior-ready answer', $plan['score_interpretation'][2]['signal']);
        $this->assertSame('L7', $plan['target_level_plan']['target_level']);
        $this->assertStringContainsString('business impact', $plan['target_level_plan']['answer_opening']);
        $this->assertSame('senior-ready', $plan['candidate_answer_review']['band']);
        $this->assertContains('explicit tradeoff', $plan['candidate_answer_review']['matched']);
        $this->assertSame('clarifying constraints', $plan['candidate_answer_review']['evidence_spans'][0]['dimension']);
        $this->assertStringContainsString('latency', $plan['candidate_answer_review']['evidence_spans'][0]['evidence']);
        $this->assertContains('Operations: name owner, metric, rollback, and review trigger.', $plan['candidate_answer_review']['rewrite_outline']);
        $this->assertSame('compression', $plan['candidate_answer_review']['gap_drills'][0]['gap']);
        $this->assertStringContainsString('Candidate System Design Answer Review', $plan['candidate_answer_review']['review_markdown']);
        $this->assertStringContainsString('System Design Interview Packet', $plan['interview_packet_markdown']);
        $this->assertCount(4, $plan['level_framing']);
    }

    /**
     * Small teams with delay tolerance should not be pushed into unnecessary realtime operations.
     */
    public function test_small_team_delayed_notifications_recommend_simple_http_delivery(): void
    {
        $plan = (new SystemDesignTradeoffPlanService)->plan([
            'scenario' => 'notification-10m',
            'latency_requirement' => 'delayed',
            'failure_impact' => 'low',
            'consistency_need' => 'eventual',
            'team_maturity' => 'small',
            'operational_capacity' => 'low',
            'current_constraint' => 'fast-delivery',
            'target_level' => 'l5',
        ]);

        $this->assertSame('long_polling', $plan['recommendation']['style']);
        $this->assertSame('not-submitted', $plan['candidate_answer_review']['status']);
        $this->assertStringContainsString('reduce moving parts', $plan['interviewer_simulation'][2]['strong_response']);
        $this->assertSame([], $plan['candidate_answer_review']['evidence_spans']);
        $this->assertContains('Decision: name the chosen architecture in one sentence.', $plan['candidate_answer_review']['rewrite_outline']);
        $this->assertSame('no answer', $plan['candidate_answer_review']['gap_drills'][0]['gap']);
        $this->assertStringContainsString('operating a distributed design the team cannot safely support', $plan['tradeoff_statement']);
        $this->assertStringContainsString('Long Polling', $plan['decision_matrix'][0]['option']);
        $this->assertContains('Polling traffic becomes more expensive than WebSocket gateway operations.', $plan['decision_review_triggers']);
        $this->assertSame('Polling without a cost ceiling', $plan['anti_patterns'][3]['pattern']);
        $this->assertSame('notification_age_p95', $plan['answer_contract']['filled_example']['first_metric']);
        $this->assertStringContainsString('Long Polling first', $plan['calibration_examples'][0]['answer']);
        $this->assertStringContainsString('simpler delivery path', $plan['scenario_variations'][0]['expected_shift']);
        $this->assertSame('L5', $plan['target_level_plan']['target_level']);
    }

    /**
     * Payment-like systems should choose correctness before throughput.
     */
    public function test_payment_flow_prioritizes_strong_consistency(): void
    {
        $plan = (new SystemDesignTradeoffPlanService)->plan([
            'scenario' => 'payment-flow',
            'latency_requirement' => 'near-real-time',
            'failure_impact' => 'high',
            'consistency_need' => 'strong',
            'team_maturity' => 'medium',
            'operational_capacity' => 'medium',
            'current_constraint' => 'regulated',
            'target_level' => 'l6',
        ]);

        $this->assertSame('strong_consistency_first', $plan['recommendation']['style']);
        $this->assertStringContainsString('idempotency keys', $plan['architecture_plan']['primary_path']);
        $this->assertContains('duplicate_rejected_count', $plan['operating_model']['metrics']);
        $this->assertSame(17, $plan['answer_scorecard']['passing_score']);
        $this->assertStringContainsString('idempotent writes', $plan['architecture_evolution_path'][0]['decision']);
        $this->assertSame('duplicate_rejected_count', $plan['answer_contract']['filled_example']['first_metric']);
        $this->assertStringContainsString('Prioritize strong consistency and idempotency before throughput', $plan['interview_packet_markdown']);
        $this->assertSame('L6', $plan['target_level_plan']['target_level']);
        $this->assertStringContainsString('If this is wrong, who pays?', $plan['decision_memo_markdown']);
    }
}
