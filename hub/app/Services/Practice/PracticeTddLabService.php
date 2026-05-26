<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeTddLabService
{
    /**
     * Compose a TDD lab from starter snippets and verification plans.
     */
    public function __construct(
        private readonly ImplementationStarterKitService $starterKits,
        private readonly ImplementationVerificationPlanService $verificationPlans,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a Red-Green-Refactor lab for one content-backed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $starterKit = $this->starterKits->build($filters);
        $verificationPlan = $this->verificationPlans->build($filters);

        if ($starterKit === null || $verificationPlan === null) {
            return null;
        }

        $blueprint = $starterKit['checklist']['blueprint'];
        $names = $blueprint['names'];

        return [
            'title' => sprintf('TDD Lab: %s', $blueprint['drill']['content']['title']),
            'source' => $blueprint['drill']['source'],
            'technology' => $blueprint['drill']['technology'],
            'record' => $blueprint['drill']['content'],
            'route' => [
                'method' => $names['route_method'],
                'path' => $names['route_path'],
            ],
            'files' => $this->filesFromNames($names),
            'cycle' => [
                [
                    'stage' => 'red',
                    'title' => 'Write the failing feature test first',
                    'goal' => sprintf('Create %s and assert behavior from the source record.', $names['test_file']),
                    'snippet' => $this->snippetByLabel($starterKit['snippets'], 'Feature test'),
                    'command' => $verificationPlan['commands'][0]['command'],
                ],
                [
                    'stage' => 'green',
                    'title' => 'Implement the smallest passing slice',
                    'goal' => 'Create the request, controller, route, and service with only the behavior needed by the test.',
                    'snippets' => collect($starterKit['snippets'])
                        ->reject(fn (array $snippet): bool => $snippet['label'] === 'Feature test')
                        ->values()
                        ->all(),
                    'smoke_request' => $verificationPlan['smoke_request'],
                ],
                [
                    'stage' => 'refactor',
                    'title' => 'Clean up and prove the implementation',
                    'goal' => 'Run the full verification set and keep the controller thin.',
                    'commands' => $verificationPlan['commands'],
                    'quality_gate_payload' => $verificationPlan['quality_gate_payload'],
                ],
            ],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Read source record and route blueprint',
                'Write failing feature test',
                'Implement request, controller, and service',
                'Run smoke request and focused test',
                'Run full suite, Pint, and quality gate',
            ]),
        ];
    }

    /**
     * Find a snippet by label.
     *
     * @param  array<int, array{label: string, file: string, code: string}>  $snippets
     * @return array{label: string, file: string, code: string}|null
     */
    private function snippetByLabel(array $snippets, string $label): ?array
    {
        return collect($snippets)
            ->first(fn (array $snippet): bool => $snippet['label'] === $label);
    }

    /**
     * Extract generated file paths from blueprint names.
     *
     * @param  array<string, string>  $names
     * @return array<int, string>
     */
    private function filesFromNames(array $names): array
    {
        return collect($names)
            ->filter(fn (string $value, string $key): bool => str_ends_with($key, '_file'))
            ->values()
            ->all();
    }
}
