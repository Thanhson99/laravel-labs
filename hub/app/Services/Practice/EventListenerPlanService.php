<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class EventListenerPlanService
{
    /**
     * Build an event/listener implementation plan for decoupled side effects.
     *
     * @param  array{event_name: string, listener_name: string, side_effect: string, queued: bool}  $input
     * @return array{event: array<string, bool|string>, listener: array<string, bool|string>, flow: array<int, string>, test_strategy: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $eventClass = Str::studly($input['event_name']);
        $listenerClass = Str::studly($input['listener_name']);
        $sideEffect = trim($input['side_effect']);

        return [
            'event' => [
                'class' => $eventClass,
                'purpose' => 'Describe that something already happened in the core workflow.',
            ],
            'listener' => [
                'class' => $listenerClass,
                'side_effect' => $sideEffect,
                'queued' => $input['queued'],
            ],
            'flow' => [
                'Keep the main service focused on the core business change.',
                'Dispatch the event after the core change succeeds.',
                'Let the listener handle the side effect.',
                'Queue the listener when the side effect is slow or external.',
            ],
            'test_strategy' => [
                'Use `Event::fake()` when testing that the event is dispatched.',
                'Test listener behavior separately with a focused unit or feature test.',
                'Use `Queue::fake()` when the listener should be queued.',
                'Avoid asserting implementation details that do not affect behavior.',
            ],
            'commands' => [
                'php artisan make:event '.$eventClass,
                'php artisan make:listener '.$listenerClass.' --event='.$eventClass,
                $input['queued'] ? 'php artisan queue:work' : 'php artisan test --filter EventListenerPlanWorkbenchTest',
                'php artisan test --filter EventListenerPlanWorkbenchTest',
            ],
        ];
    }
}
