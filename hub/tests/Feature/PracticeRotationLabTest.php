<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeRotationLabTest extends TestCase
{
    /**
     * The rotation lab page renders a day-by-day schedule.
     */
    public function test_rotation_lab_page_renders_schedule(): void
    {
        $response = $this->get('/practice/rotation-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Rotation lab turns the mastery path into a daily practice schedule.')
            ->assertSee('Daily Rotation')
            ->assertSee('Day 1')
            ->assertSee('Rotation Progress Payload');
    }

    /**
     * The rotation lab API returns schedule items with lab links.
     */
    public function test_rotation_lab_api_returns_schedule(): void
    {
        $response = $this->getJson('/api/practice/rotation-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.day_count', 3)
            ->assertJsonPath('data.schedule.0.focus', 'Build capstone task')
            ->assertJsonPath('data.schedule.0.capstone_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'schedule' => [
                        '*' => [
                            'day',
                            'technology',
                            'focus',
                            'capstone_query',
                            'checkpoint_query',
                            'mentor_feedback_query',
                            'output',
                        ],
                    ],
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * Predictive AI rotations produce AI type comparison outputs.
     */
    public function test_predictive_generative_ai_rotation_uses_ai_type_outputs(): void
    {
        $this->getJson('/api/practice/rotation-lab?family=vibe-coding&language=en&search=Predictive%20AI&phase_limit=1&tasks_per_phase=1&days=3')
            ->assertOk()
            ->assertJsonPath('data.schedule.0.technology', 'llm-foundations')
            ->assertJsonPath('data.schedule.0.output', 'AI type comparison table with output contracts')
            ->assertJsonPath('data.schedule.1.output', 'Checkpoint evidence for metrics and failure modes')
            ->assertJsonPath('data.schedule.2.output', 'Mentor feedback action item for predictive and generative evidence')
            ->assertJsonPath('data.schedule.0.capstone_query.technology', 'llm-foundations');
    }
}
