<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentPracticeWorkbenchLinkService
{
    /**
     * Return the closest runnable workbench for a mapped content technology.
     *
     * @return array{label: string, path: string, route_name: string|null, concept: string}|null
     */
    public function linkFor(string $technology): ?array
    {
        return match ($technology) {
            'php' => [
                'label' => 'Open PHP name normalizer workbench',
                'path' => '/workbench/name-normalizer',
                'route_name' => 'practice.workbench.name-normalizer',
                'concept' => 'Typed PHP input normalization and unit-testable behavior.',
            ],
            'api-validation' => [
                'label' => 'Open API validation workbench',
                'path' => '/workbench/topic-intake',
                'route_name' => 'practice.workbench.topic-intake',
                'concept' => 'Form Request validation, thin API controller, and JSON response shape.',
            ],
            'laravel-http' => [
                'label' => 'Open HTTP request-flow workbench',
                'path' => '/workbench/http-request-flow',
                'route_name' => 'practice.workbench.http-request-flow',
                'concept' => 'Route, validation, controller, service, and response flow.',
            ],
            'testing-quality' => [
                'label' => 'Open quality-gate workbench',
                'path' => '/workbench/quality-gate',
                'route_name' => 'practice.workbench.quality-gate',
                'concept' => 'Focused tests, verification commands, and done criteria.',
            ],
            'docker-runtime' => [
                'label' => 'Open runtime smoke-check API',
                'path' => '/api/practice/runtime-smoke-check',
                'route_name' => 'api.practice.runtime-smoke-check',
                'concept' => 'Runtime configuration, Docker assumptions, and environment checks.',
            ],
            'ai-workflow' => [
                'label' => 'Open review and quality-gate workbench',
                'path' => '/workbench/quality-gate',
                'route_name' => 'practice.workbench.quality-gate',
                'concept' => 'AI-generated code review, focused verification, and quality evidence.',
            ],
            'interview' => [
                'label' => 'Open interview defense lab',
                'path' => '/practice/interview-defense-lab',
                'route_name' => 'practice.interview-defense-lab',
                'concept' => 'Turn interview answers into evidence-backed implementation defense.',
            ],
            default => null,
        };
    }
}
