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
            ->assertSee('Technology Tracks');
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
