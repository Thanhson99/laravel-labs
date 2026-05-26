<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeTopicWorkbenchTest extends TestCase
{
    /**
     * The topic-intake workbench renders the runnable API validation loop.
     */
    public function test_topic_intake_workbench_renders_api_validation_loop(): void
    {
        $response = $this->get('/workbench/topic-intake');

        $response
            ->assertOk()
            ->assertSee('Practice Topic Intake Workbench')
            ->assertSee('POST /api/practice/topics')
            ->assertSee('StorePracticeTopicRequest')
            ->assertSee('PracticeTopicIntakeService')
            ->assertSee('Submit to API');
    }

    /**
     * The configured API exercise links to the runnable workbench.
     */
    public function test_api_validation_exercise_links_to_topic_intake_workbench(): void
    {
        $response = $this->get('/practice/api-form-request-slice');

        $response
            ->assertOk()
            ->assertSee('Build a validated practice API endpoint')
            ->assertSee('Run the API validation workbench')
            ->assertSee('/workbench/topic-intake');
    }
}
