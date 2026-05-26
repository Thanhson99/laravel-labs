<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyQualityPlanService
{
    /**
     * Create quality plans for technology learning pipelines.
     */
    public function __construct(
        private readonly TechnologyPipelineIndexService $pipelines,
        private readonly PracticeQualityGateService $qualityGate,
    ) {}

    /**
     * Build a quality plan for every filtered technology pipeline.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $pipelines = $this->pipelines->build($filters);

        $items = collect($pipelines['items'])
            ->map(fn (array $item): array => $this->toQualityItem($item))
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                ...$pipelines['meta'],
                'quality_plan_count' => count($items),
                'minimum_status' => $this->minimumStatus($items),
            ],
        ];
    }

    /**
     * Convert one pipeline card into a quality plan card.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function toQualityItem(array $item): array
    {
        $baseline = $this->baselineFor($item);
        $gate = $this->qualityGate->evaluate($baseline);

        return [
            'technology' => $item['technology'],
            'record_count' => $item['record_count'],
            'source_count' => $item['source_count'],
            'pipeline_route' => $item['pipeline_route'],
            'api_pipeline_route' => $item['api_pipeline_route'],
            'quality_gate' => $gate,
            'baseline' => $baseline,
            'commands' => [
                'php artisan test --filter Technology'.str($item['technology'])->studly()->replace('-', '')->toString().'Test',
                'php artisan test --filter RouteFileDocumentationTest',
                'vendor\\bin\\pint --test',
            ],
            'acceptance_checks' => $this->acceptanceChecksFor((string) $item['technology']),
            'risk_note' => $this->riskNoteFor((string) $item['technology']),
        ];
    }

    /**
     * Build recommended verification counts for one technology.
     *
     * @param  array<string, mixed>  $item
     * @return array{tests: int, assertions: int, failures: int, pint: bool}
     */
    private function baselineFor(array $item): array
    {
        $tests = max(2, min(12, (int) ceil(((int) $item['record_count']) / 4)));

        return [
            'tests' => $tests,
            'assertions' => max($tests * 4, (int) $item['source_count'] * 3),
            'failures' => 0,
            'pint' => true,
        ];
    }

    /**
     * Return acceptance checks tailored to one technology family.
     *
     * @return array<int, string>
     */
    private function acceptanceChecksFor(string $technology): array
    {
        return match ($technology) {
            'api-validation' => [
                'Request validation rejects malformed payloads.',
                'Controller returns a stable JSON response shape.',
                'Feature tests cover success and validation failure.',
            ],
            'auth-security' => [
                'Authorization is enforced outside Blade-only checks.',
                'Sensitive data is excluded from responses and logs.',
                'Tests cover allowed and denied access paths.',
            ],
            'database-eloquent' => [
                'Queries select only the fields needed by the screen.',
                'List views avoid obvious N+1 query paths.',
                'Tests cover filtering and empty result behavior.',
            ],
            'performance-cache' => [
                'Cache keys include the filters that change output.',
                'Invalidation or freshness expectations are documented.',
                'Tests prove uncached and cached responses stay equivalent.',
            ],
            'testing-quality' => [
                'Behavior tests assert output, not implementation details.',
                'Pint passes before refactor work is considered complete.',
                'Failure messages point to the next concrete fix.',
            ],
            default => [
                'A feature test covers the primary happy path.',
                'At least one edge case or failure path is covered.',
                'Pint and focused tests pass before moving to portfolio work.',
            ],
        };
    }

    /**
     * Return the main risk to watch while practicing one technology.
     */
    private function riskNoteFor(string $technology): string
    {
        return match ($technology) {
            'api-validation' => 'Validation can drift from the documented API shape when controllers start normalizing input manually.',
            'auth-security' => 'Security practice is incomplete if authorization is only represented as UI visibility.',
            'database-eloquent' => 'Data practice can look correct on small JSON samples while hiding inefficient query boundaries.',
            'performance-cache' => 'Cache examples are risky when freshness rules are not stated next to the implementation.',
            'testing-quality' => 'Test-count targets are only useful when assertions protect learner-visible behavior.',
            default => 'Keep the quality gate tied to observable behavior so the exercise remains practical instead of checklist-only.',
        };
    }

    /**
     * Summarize the lowest quality status across all plan items.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private function minimumStatus(array $items): string
    {
        return collect($items)
            ->contains(fn (array $item): bool => $item['quality_gate']['passed'] === false)
                ? 'needs-work'
                : 'ready';
    }
}
