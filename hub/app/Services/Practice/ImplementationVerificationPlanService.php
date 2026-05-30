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
            'commands' => $this->commandsFor($names),
            'smoke_request' => $this->smokeRequestFor($workspace, $names),
            'quality_gate_payload' => [
                'tests' => 1,
                'assertions' => $this->assertionCountFor($workspace),
                'failures' => 0,
                'pint' => true,
            ],
            'done_when' => $this->doneWhenFor($workspace),
        ];
    }

    /**
     * Return verification commands for one generated implementation.
     *
     * @param  array<string, string>  $names
     * @return array<int, array{label: string, command: string}>
     */
    private function commandsFor(array $names): array
    {
        return [
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
        ];
    }

    /**
     * Return the smoke request for one technology.
     *
     * @param  array<string, string>  $names
     * @return array<string, mixed>
     */
    private function smokeRequestFor(array $workspace, array $names): array
    {
        $technology = $workspace['technology'];

        if ($technology === 'idor-access-control') {
            return [
                'method' => $names['route_method'],
                'path' => $names['route_path'],
                'headers' => [
                    'Accept' => 'text/html,application/json',
                ],
                'expected_text' => [
                    'object-level authorization',
                    'scoped lookup',
                    'policy check',
                    'ID-swap denial test',
                ],
                'denial_probe' => [
                    'attacker' => 'user_a',
                    'victim_object' => 'user_b_invoice_id',
                    'expected_status' => 403,
                    'fallback_status' => 404,
                ],
            ];
        }

        if ($this->isArrowThisWorkspace($workspace)) {
            return [
                'method' => $names['route_method'],
                'path' => $names['route_path'],
                'headers' => [
                    'Accept' => 'text/html,application/json',
                ],
                'expected_text' => [
                    'lexical this',
                    'dynamic this',
                    'obj.arrow() trap',
                    'call/apply/bind cannot rebind arrow this',
                ],
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'method' => $names['route_method'],
                'path' => $names['route_path'],
                'headers' => [
                    'Accept' => 'text/html,application/json',
                ],
                'expected_text' => [
                    'lexical scope',
                    'captured binding',
                    'createCounter',
                    'stale closure',
                ],
            ];
        }

        return [
            'method' => $names['route_method'],
            'path' => $names['route_path'],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => [
                'title' => 'Practice from content record',
            ],
        ];
    }

    /**
     * Return completion criteria for one technology.
     *
     * @return array<int, string>
     */
    private function doneWhenFor(array $workspace): array
    {
        $technology = $workspace['technology'];

        if ($technology === 'idor-access-control') {
            return [
                'The focused IDOR test passes for the generated test class.',
                'The generated route appears in route:list.',
                'The smoke request renders object-level authorization, scoped lookup, policy check, and ID-swap denial evidence.',
                'The denial probe proves user A cannot access user B object with 403 or deliberately scoped 404 behavior.',
                'The quality gate reports ready after full suite and Pint pass.',
            ];
        }

        if ($this->isArrowThisWorkspace($workspace)) {
            return [
                'The focused arrow-this test passes for the generated test class.',
                'The generated route appears in route:list.',
                'The smoke request renders lexical this, dynamic this, obj.arrow() trap, and call/apply/bind evidence.',
                'The quality gate reports ready after full suite and Pint pass.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'The focused closure test passes for the generated test class.',
                'The generated route appears in route:list.',
                'The smoke request renders lexical scope, captured binding, createCounter(), and stale-closure evidence.',
                'The quality gate reports ready after full suite and Pint pass.',
            ];
        }

        return [
            'The focused test passes for the generated test class.',
            'The generated route appears in route:list.',
            'The smoke request returns the expected JSON shape.',
            'The quality gate reports ready after full suite and Pint pass.',
        ];
    }

    /**
     * Return expected assertion count for the generated starter test.
     */
    private function assertionCountFor(array $workspace): int
    {
        return match ($workspace['technology']) {
            'idor-access-control', 'javascript-closures' => 4,
            default => 1,
        };
    }

    /**
     * Detect arrow-function `this` workspaces in the JavaScript closure lane.
     *
     * @param  array<string, mixed>  $workspace
     */
    private function isArrowThisWorkspace(array $workspace): bool
    {
        if (($workspace['technology'] ?? null) !== 'javascript-closures') {
            return false;
        }

        $haystack = strtolower(implode(' ', [
            $workspace['content']['title'] ?? '',
            $workspace['content']['body'] ?? '',
            $workspace['blueprint']['drill']['goal'] ?? '',
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }
}
