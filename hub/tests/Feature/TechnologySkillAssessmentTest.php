<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologySkillAssessmentTest extends TestCase
{
    /**
     * The technology skill assessment page renders a scored rubric and improvement tasks.
     */
    public function test_technology_skill_assessment_page_renders_rubric_and_improvement_tasks(): void
    {
        $response = $this->get('/practice/technology-skill-assessment/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Skill Assessment: api-validation')
            ->assertSee('Pass score 80 / 100')
            ->assertSee('Source understanding')
            ->assertSee('Laravel layer placement')
            ->assertSee('Improvement Tasks')
            ->assertSee('Open assessment API');
    }

    /**
     * The technology skill assessment API returns rubric rows and readiness signals.
     */
    public function test_technology_skill_assessment_api_returns_rubric_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-skill-assessment/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.max_score', 100)
            ->assertJsonPath('data.pass_score', 80)
            ->assertJsonPath('data.rubric.0.criterion', 'Source understanding')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Source understanding')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'interview_pack',
                    'rubric' => [
                        '*' => [
                            'criterion',
                            'points',
                            'evidence',
                            'pass_signal',
                        ],
                    ],
                    'max_score',
                    'pass_score',
                    'readiness',
                    'improvement_tasks',
                    'progress_payload',
                ],
            ]);
    }
}
