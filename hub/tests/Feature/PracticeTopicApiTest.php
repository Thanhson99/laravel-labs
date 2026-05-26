<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeTopicApiTest extends TestCase
{
    /**
     * A valid topic payload returns a stable practice response.
     */
    public function test_practice_topic_api_accepts_valid_payload(): void
    {
        $response = $this->postJson('/api/practice/topics', [
            'title' => 'Build a validated API slice',
            'track' => 'api-validation',
            'difficulty' => 'beginner',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.id', 'api-validation-build-a-validated-api-slice')
            ->assertJsonPath('data.title', 'Build a validated API slice')
            ->assertJsonPath('data.track', 'api-validation')
            ->assertJsonPath('data.difficulty', 'beginner')
            ->assertJsonPath('data.next_action', 'Write the API validation test first, then implement the Form Request and JSON response.')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter PracticeTopic')
            ->assertJsonPath('message', 'Practice topic accepted.');
    }

    /**
     * Invalid topic payloads return Laravel validation errors.
     */
    public function test_practice_topic_api_rejects_invalid_payload(): void
    {
        $response = $this->postJson('/api/practice/topics', [
            'title' => 'No',
            'track' => 'unknown-track',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'track']);
    }
}
