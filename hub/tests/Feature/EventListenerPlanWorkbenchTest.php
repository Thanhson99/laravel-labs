<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class EventListenerPlanWorkbenchTest extends TestCase
{
    /**
     * The event/listener workbench renders the side-effect planning loop.
     */
    public function test_event_listener_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/event-listener-plan');

        $response
            ->assertOk()
            ->assertSee('Event Listener Plan Workbench')
            ->assertSee('POST /api/practice/event-listener-plan')
            ->assertSee('EventListenerPlanService')
            ->assertSee('Plan event flow');
    }

    /**
     * The event/listener API returns classes, flow, test strategy, and commands.
     */
    public function test_event_listener_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/event-listener-plan', [
            'event_name' => 'Practice Task Completed',
            'listener_name' => 'Send Practice Completion Notification',
            'side_effect' => 'Send learner notification',
            'queued' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.event.class', 'PracticeTaskCompleted')
            ->assertJsonPath('data.listener.class', 'SendPracticeCompletionNotification')
            ->assertJsonPath('data.listener.queued', true)
            ->assertJsonPath('data.flow.0', 'Keep the main service focused on the core business change.')
            ->assertJsonPath('data.commands.0', 'php artisan make:event PracticeTaskCompleted');
    }

    /**
     * Invalid event/listener payloads return validation errors.
     */
    public function test_event_listener_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/event-listener-plan', [
            'event_name' => '<bad>',
            'listener_name' => 'x',
            'side_effect' => '',
            'queued' => 'sometimes',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['event_name', 'listener_name', 'side_effect', 'queued']);
    }
}
