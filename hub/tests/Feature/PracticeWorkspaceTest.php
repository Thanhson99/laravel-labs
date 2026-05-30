<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeWorkspaceTest extends TestCase
{
    /**
     * The practice workspace renders native Laravel exercises.
     */
    public function test_practice_workspace_renders_native_exercises(): void
    {
        $response = $this->get('/practice');

        $response
            ->assertOk()
            ->assertSee('Practice Workspace')
            ->assertSee('Build a PHP CLI input normalizer')
            ->assertSee('Technology Tracks')
            ->assertSee('Frontend Performance')
            ->assertSee('Security + Auth')
            ->assertSee('Optimize React re-renders with memoization discipline')
            ->assertSee('PKCE board')
            ->assertSee('PKCE pipeline')
            ->assertSee('PKCE workbench')
            ->assertSee('Stack/Heap pipelines')
            ->assertSee('Stack/Heap pipeline')
            ->assertSee('Stack/Heap lab')
            ->assertSee('Stack/Heap interview');
    }

    /**
     * The exercise detail page gives code-focused instructions.
     */
    public function test_practice_exercise_detail_renders_tasks_files_and_commands(): void
    {
        $response = $this->get('/practice/api-form-request-slice');

        $response
            ->assertOk()
            ->assertSee('Build a validated practice API endpoint')
            ->assertSee('Files To Touch')
            ->assertSee('Commands')
            ->assertSee('Done When');
    }

    /**
     * The practice API exposes configured exercises.
     */
    public function test_practice_api_returns_exercises(): void
    {
        $response = $this->getJson('/api/practice?track=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.track', 'api-validation')
            ->assertJsonPath('data.exercises.0.slug', 'api-form-request-slice');
    }

    /**
     * Frontend performance has a first-class practice track for React render work.
     */
    public function test_practice_api_filters_frontend_performance_exercises(): void
    {
        $response = $this->getJson('/api/practice?track=frontend-performance');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.track', 'frontend-performance')
            ->assertJsonPath('data.exercises.0.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.exercises.0.track', 'frontend-performance');
    }

    /**
     * Security and auth has a first-class practice track for SQL Injection and token topics.
     */
    public function test_practice_api_filters_security_auth_exercises(): void
    {
        $response = $this->getJson('/api/practice?track=security-auth&search=SQL%20Injection');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.track', 'security-auth')
            ->assertJsonPath('data.exercises.0.slug', 'sql-injection-defense-plan-workbench')
            ->assertJsonPath('data.exercises.0.track', 'security-auth');
    }

    /**
     * AI review exposes the dedicated AI agent memory planning exercise.
     */
    public function test_practice_api_filters_ai_agent_memory_exercise(): void
    {
        $response = $this->getJson('/api/practice?track=ai-review&search=agent%20memory');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.track', 'ai-review')
            ->assertJsonPath('data.exercises.0.slug', 'ai-agent-memory-plan-workbench')
            ->assertJsonPath('data.exercises.0.workbench.route', 'practice.workbench.ai-agent-memory-plan')
            ->assertJsonPath('data.exercises.0.api.path', '/api/practice/ai-agent-memory-plan');
    }

    /**
     * Security and auth exposes the dedicated IDOR access-review exercise.
     */
    public function test_practice_api_filters_idor_access_review_exercise(): void
    {
        $response = $this->getJson('/api/practice?track=security-auth&search=IDOR');

        $response
            ->assertOk()
            ->assertJsonPath('meta.filters.track', 'security-auth')
            ->assertJsonPath('data.exercises.0.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.exercises.0.workbench.route', 'practice.workbench.idor-access-review')
            ->assertJsonPath('data.exercises.0.api.path', '/api/practice/idor-access-review');
    }

    /**
     * The practice API validates catalog filter lengths.
     */
    public function test_practice_api_validates_catalog_filters(): void
    {
        $response = $this->getJson('/api/practice?track='.str_repeat('x', 81));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('track');
    }

    /**
     * The practice API returns 404 for an unknown exercise.
     */
    public function test_practice_api_returns_not_found_for_unknown_exercise(): void
    {
        $response = $this->getJson('/api/practice/not-found');

        $response->assertNotFound();
    }
}
