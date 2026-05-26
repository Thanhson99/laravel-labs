<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeNoteService
{
    /**
     * Return the first thin-controller practice note.
     *
     * @return array{title: string, summary: string, checklist: array<int, string>, next_step: string}
     */
    public function first(): array
    {
        return [
            'title' => 'Thin controllers keep Laravel practice focused',
            'summary' => 'A controller should translate an HTTP request into an application call and return a response. Formatting, lookup rules, and branching belong in services or dedicated objects.',
            'checklist' => [
                'Route points to a named controller action.',
                'Controller receives a service through dependency injection.',
                'Service owns the data shape used by the view.',
                'Blade renders values with escaped output.',
                'Feature test checks visible behavior.',
            ],
            'next_step' => 'Change the note data in the service, then update the feature test to describe the new behavior.',
        ];
    }
}
