<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class SystemDesignTradeoffPlanWorkbenchTest extends TestCase
{
    /**
     * The workbench page renders the runnable System Design tradeoff planner.
     */
    public function test_workbench_page_renders(): void
    {
        $this->get('/workbench/system-design-tradeoff-plan')
            ->assertOk()
            ->assertSee('Answer System Design', false)
            ->assertSee('system-design-tradeoff-plan');
    }

    /**
     * The API returns a tradeoff-first plan for valid input.
     */
    public function test_api_returns_system_design_tradeoff_plan(): void
    {
        $this->postJson('/api/practice/system-design-tradeoff-plan', [
            'scenario' => 'notification-10m',
            'latency_requirement' => 'real-time',
            'failure_impact' => 'high',
            'consistency_need' => 'eventual',
            'team_maturity' => 'platform',
            'operational_capacity' => 'high',
            'current_constraint' => 'greenfield',
            'target_level' => 'l7',
            'candidate_answer' => 'I would clarify latency and outage impact first. I choose WebSocket, but the tradeoff is operational cost. I reject polling instead for this real-time case. The platform team owns operations, I would monitor fanout lag, revisit on an SLO trigger, and keep rollback for gateway failure.',
        ])
            ->assertOk()
            ->assertJsonPath('data.recommendation.style', 'websocket_event_stream')
            ->assertJsonPath('data.clarifying_questions.0', 'Does notification delivery need to be real-time, near-real-time, or can it be delayed by 30 seconds?')
            ->assertJsonPath('data.operating_model.metrics.1', 'fanout_lag_ms')
            ->assertJsonPath('data.answer_scorecard.passing_score', 15)
            ->assertJsonPath('data.anti_patterns.0.pattern', 'Tool-first answer')
            ->assertJsonPath('data.architecture_evolution_path.0.phase', 'Phase 1: smallest safe design')
            ->assertJsonPath('data.answer_contract.filled_example.first_metric', 'fanout_lag_ms')
            ->assertJsonPath('data.review_checklist.0.item', 'Clarifying questions')
            ->assertJsonPath('data.calibration_examples.2.level', 'strong')
            ->assertJsonPath('data.drill_cards.0.front', 'What is the first move in a System Design interview?')
            ->assertJsonPath('data.decision_tree.0.question', 'Is real-time delivery a hard requirement?')
            ->assertJsonPath('data.scenario_variations.2.change', 'Shrink the team to three engineers without platform support.')
            ->assertJsonPath('data.score_interpretation.2.range', '15-20')
            ->assertJsonPath('data.target_level_plan.target_level', 'L7')
            ->assertJsonPath('data.target_level_plan.answer_opening', 'Before architecture, I would clarify the business impact, SLA, organization maturity, and cost of being wrong.')
            ->assertJsonPath('data.candidate_answer_review.band', 'senior-ready')
            ->assertJsonPath('data.candidate_answer_review.score', 20)
            ->assertJsonPath('data.interviewer_simulation.1.interviewer', 'What breaks first in production?')
            ->assertJsonPath('data.interviewer_simulation.1.strong_response', fn (string $answer): bool => str_contains($answer, 'fanout_lag_ms'))
            ->assertJsonPath('data.candidate_answer_review.evidence_spans.0.dimension', 'clarifying constraints')
            ->assertJsonPath('data.candidate_answer_review.rewrite_outline.0', 'Context: state latency, platform, outage impact, duplicate tolerance, and team capacity.')
            ->assertJsonPath('data.candidate_answer_review.gap_drills.0.gap', 'compression')
            ->assertJsonPath('data.candidate_answer_review.review_markdown', fn (string $markdown): bool => str_contains($markdown, 'Candidate System Design Answer Review'))
            ->assertJsonPath('data.one_minute_answer', fn (string $answer): bool => str_contains($answer, 'I would not draw first'))
            ->assertJsonPath('data.interview_packet_markdown', fn (string $packet): bool => str_contains($packet, 'System Design Interview Packet'))
            ->assertJsonPath('data.rewrite_practice.rule', 'Replace tool-first claims with context, accepted cost, rejected alternative, owner, and review trigger.')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter SystemDesignTradeoffPlan');
    }

    /**
     * Invalid option values are rejected by the Form Request.
     */
    public function test_api_validates_tradeoff_input(): void
    {
        $this->postJson('/api/practice/system-design-tradeoff-plan', [
            'scenario' => 'notification-10m',
            'latency_requirement' => 'instant',
            'target_level' => 'l9',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['latency_requirement', 'failure_impact', 'target_level']);
    }
}
