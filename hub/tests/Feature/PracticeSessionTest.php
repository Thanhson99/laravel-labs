<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeSessionTest extends TestCase
{
    /**
     * The practice session page renders native exercises and commands.
     */
    public function test_practice_session_page_renders_daily_plan(): void
    {
        $response = $this->get('/practice-sessions/today?track=api-validation');

        $response
            ->assertOk()
            ->assertSee('Today Practice Session')
            ->assertSee('Build a validated practice API endpoint')
            ->assertSee('php artisan test');
    }

    /**
     * The practice session API returns filtered exercises.
     */
    public function test_practice_session_api_returns_filtered_plan(): void
    {
        $response = $this->getJson('/api/practice/session-plan?track=testing-quality');

        $response
            ->assertOk()
            ->assertJsonPath('data.focus', 'testing-quality')
            ->assertJsonPath('data.exercises.0.track', 'testing-quality')
            ->assertJsonPath('meta.track', 'testing-quality');
    }

    /**
     * The practice session API validates track filter lengths.
     */
    public function test_practice_session_api_validates_track_filter(): void
    {
        $response = $this->getJson('/api/practice/session-plan?track='.str_repeat('x', 81));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('track');
    }
}
