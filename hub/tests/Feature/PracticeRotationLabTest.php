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
}
