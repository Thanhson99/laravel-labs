<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ImplementationVerificationPlanService
{
    /**
     * Create verification plans from record workspaces.
     */
    public function __construct(private readonly RecordPracticeWorkspaceService $workspaces) {}

    /**
     * Build verification steps for one content-backed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $workspace = $this->workspaces->build($filters);

        if ($workspace === null) {
            return null;
        }

        $names = $workspace['blueprint']['names'];

        return [
            'title' => sprintf('Verification Plan: %s', $workspace['content']['title']),
            'source' => $workspace['source'],
            'technology' => $workspace['technology'],
            'record_id' => $workspace['content']['title'],
            'commands' => [
                [
                    'label' => 'Focused test',
                    'command' => sprintf('php artisan test --filter %s', $names['test_class']),
                ],
                [
                    'label' => 'Route check',
                    'command' => 'php artisan route:list --path=api/practice',
                ],
                [
                    'label' => 'Full suite',
                    'command' => 'php artisan test',
                ],
                [
                    'label' => 'Style',
                    'command' => 'vendor\\bin\\pint --test',
                ],
            ],
            'smoke_request' => [
                'method' => $names['route_method'],
                'path' => $names['route_path'],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'body' => [
                    'title' => 'Practice from content record',
                ],
            ],
            'quality_gate_payload' => [
                'tests' => 1,
                'assertions' => 1,
                'failures' => 0,
                'pint' => true,
            ],
            'done_when' => [
                'The focused test passes for the generated test class.',
                'The generated route appears in route:list.',
                'The smoke request returns the expected JSON shape.',
                'The quality gate reports ready after full suite and Pint pass.',
            ],
        ];
    }
}
