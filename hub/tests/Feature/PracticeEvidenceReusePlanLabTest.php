<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeEvidenceReusePlanLabTest extends TestCase
{
    /**
     * The evidence reuse plan page renders reuse sections.
     */
    public function test_evidence_reuse_plan_lab_page_renders_reuse_sections(): void
    {
        $response = $this->get('/practice/evidence-reuse-plan-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Evidence reuse planning turns retrieval cards into portfolio and interview tasks.')
            ->assertSee('Reuse Rules')
            ->assertSee('Reuse Plans')
            ->assertSee('Evidence Reuse Plan Progress Payload');
    }

    /**
     * The evidence reuse plan API returns reuse plans from retrieval cards.
     */
    public function test_evidence_reuse_plan_lab_api_returns_reuse_payload(): void
    {
        $response = $this->getJson('/api/practice/evidence-reuse-plan-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_retrieval_lab.card_count', 3)
            ->assertJsonPath('data.reuse_summary.plan_count', 3)
            ->assertJsonPath('data.reuse_summary.portfolio_plan_count', 3)
            ->assertJsonPath('data.reuse_summary.refresh_plan_count', 0)
            ->assertJsonPath('data.reuse_plans.0.reuse_mode', 'portfolio-and-interview')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_retrieval_lab',
                    'reuse_rules',
                    'reuse_plans' => [
                        '*' => [
                            'plan',
                            'technology_segment',
                            'route_name',
                            'reuse_mode',
                            'source_prompt',
                            'portfolio_task',
                            'interview_task',
                            'review_task',
                            'proof_inputs',
                            'quality_check',
                        ],
                    ],
                    'reuse_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
